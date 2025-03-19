<?php

use MediaWiki\Maintenance\Maintenance;
use Wikimedia\Rdbms\IDatabase;

require_once 'BSMaintenance.php';

class PrepareActorMigration extends Maintenance {

	public function __construct() {
		parent::__construct();

		$this->addOption( 'unknownUserId', 'The ID that should be assigned', true, true );
		$this->addOption( 'unknownUserName', 'The Name that should be assigned', true, true );

		$this->requireExtension( 'BlueSpiceFoundation' );
	}

	/**
	 *
	 * @var IDatabase
	 */
	private $db = null;

	private $userIds = [];
	private $userNames = [];

	private $unknownUserId = 1;
	private $unknownUserName = 'WikiSysop';

	public function execute() {
		$this->unknownUserId = $this->getOption( 'unknownUserId', 1 );
		$this->unknownUserName = $this->getOption( 'unknownUserName', 'WikiSysop' );
		$this->db = $this->getDB( DB_PRIMARY );
		$this->fetchAllUsers();
		$this->searchForNonExistingUsers();
		$this->outputUnknown();
		$this->fixData();
	}

	private function fetchAllUsers() {
		$res = $this->db->select(
			'user',
			[ 'user_id', 'user_name' ],
			'',
			__METHOD__
		);
		foreach ( $res as $row ) {
			$this->userIds[] = (int)$row->user_id;
			$this->userNames[] = $row->user_name;
		}
	}

	private function outputUnknown() {
		$userIdList = implode( ', ', $this->orphanedUserIds );
		$userNameList = implode( ', ', $this->orphanedUserNames );
		$this->output( "User ids to be replaced: $userIdList\n" );
		$this->output( "User names to be replaced: $userNameList\n" );
	}

	private $tablesToFix = [
		'revision' => [ 'rev_user', 'rev_user_text' ],
		'archive' => [ 'ar_user', 'ar_user_text' ],
		// This script is meant for use in legacy version 1.35 only,
		// therefore the reference to the `ipblocks` table can remain
		'ipblocks' => [ 'ipb_by', 'ipb_by_text' ],
		'image' => [ 'img_user', 'img_user_text' ],
		'oldimage' => [ 'oi_user', 'oi_user_text' ],
		'filearchive' => [ 'fa_user', 'fa_user_text' ],
		'recentchanges' => [ 'rc_user', 'rc_user_text' ],
		'logging' => [ 'log_user', 'log_user_text' ],
	];

	private function searchForNonExistingUsers() {
		foreach ( $this->tablesToFix as $tableName => $columns ) {
			$this->output( "Search table '$tableName' ...\n" );
			$this->checkUserId( $tableName, $columns[0] );
			$this->checkUserText( $tableName, $columns[1] );
		}

		$this->orphanedUserIds = array_unique( $this->orphanedUserIds );
		$this->orphanedUserNames = array_unique( $this->orphanedUserNames );
		sort( $this->orphanedUserIds );
		sort( $this->orphanedUserNames );
	}

	private $orphanedUserIds = [];

	private function checkUserId( $tableName, $fieldName ) {
		$res = $this->db->select(
			$tableName,
			"DISTINCT ($fieldName) AS userid",
			'',
			__METHOD__
		);
		$userIds = [];
		foreach ( $res as $row ) {
			$userIds[] = (int)$row->userid;
		}

		$orphanedUserIds = array_diff( $userIds, $this->userIds );

		$this->orphanedUserIds = array_merge( $this->orphanedUserIds, $orphanedUserIds );
	}

	private $orphanedUserNames = [];

	private function checkUserText( $tableName, $fieldName ) {
		$res = $this->db->select(
			$tableName, "DISTINCT ($fieldName) AS username",
			'',
			__METHOD__
		);
		$userNames = [];
		foreach ( $res as $row ) {
			$userNames[] = $row->username;
		}

		$orphanedUserNames = array_diff( $userNames, $this->userNames );

		$this->orphanedUserNames = array_merge( $this->orphanedUserNames, $orphanedUserNames );
	}

	private function fixData() {
		foreach ( $this->tablesToFix as $tableName => $columns ) {
			$this->output( "Fix table '$tableName' ...\n" );
			$userIdField = $columns[0];
			$userNameField = $columns[1];
			$this->db->update(
				$tableName,
				[ $userIdField => $this->unknownUserId ],
				[ $userIdField => $this->orphanedUserIds ],
				__METHOD__
			);
			$this->db->update(
				$tableName,
				[ $userNameField => $this->unknownUserName ],
				[ $userNameField => $this->orphanedUserNames ],
				__METHOD__
			);
		}
	}
}

$maintClass = PrepareActorMigration::class;
require_once RUN_MAINTENANCE_IF_MAIN;
