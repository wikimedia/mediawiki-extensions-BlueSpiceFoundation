<?php

use MediaWiki\Maintenance\Maintenance;
use MediaWiki\MediaWikiServices;
use Wikimedia\Rdbms\IDatabase;

require_once 'Maintenance.php';

class PrepareActorMigration2 extends Maintenance {

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

	public function __construct() {
		parent::__construct();

		$this->addOption( 'dry', 'Do not actually perform the change' );
	}

	/**
	 *
	 * @var IDatabase
	 */
	private $db = null;

	/**
	 *
	 * @var \MediaWiki\User\UserNameUtils
	 */
	private $userNameUtils = null;

	private $dry = true;
	private $userNames = [];

	public function execute() {
		$this->dry = (bool)$this->getOption( 'dry', false );
		$this->db = $this->getDB( DB_PRIMARY );
		$this->userNameUtils = MediaWikiServices::getInstance()->getUserNameUtils();
		$this->fetchAllUsers();
		foreach ( $this->tablesToFix as $table => $fields ) {
			$userIDField = $fields[0];
			$userNameField = $fields[1];
			$this->fixTable( $table, $userIDField, $userNameField );
		}
	}

	private function fetchAllUsers() {
		$res = $this->db->select(
			'user',
			[ 'user_name' ],
			'',
			__METHOD__
		);
		foreach ( $res as $row ) {
			$this->userNames[] = $row->user_name;
		}
	}

	private function fixTable( $table, $userIDField, $userNameField ) {
		$this->output( "Replacing user names in $table\n" );

		$res = $this->db->select(
			$table,
			[ $userNameField ],
			'',
			__METHOD__
		);
		$userNameReplacements = [];
		foreach ( $res as $row ) {
			$userName = $row->$userNameField;
			$userNameExists = in_array( $userName, $this->userNames );
			if ( $userNameExists ) {
				continue;
			}

			$newUserName = substr( "deleted>$userName", 0, 255 );
			if ( !$this->userNameUtils->isUsable( $userName ) ) {
				// If the user name is not usable anyways, we do not need to update it.
				// Example: "imported>FooUser"
				// We still need to store it, as we need to make sure the
				// foreign key to user_id still gets set to 0.
				$newUserName = $userName;
			}

			$userNameReplacements[$userName] = $newUserName;
		}
		ksort( $userNameReplacements );

		foreach ( $userNameReplacements as $oldUserName => $newUserName ) {
			$this->output( "* $oldUserName -> $newUserName" );

			if ( $this->dry ) {
				$this->output( " -> DRYRUN)\n" );
				continue;
			}

			$success = $this->db->update(
				$table,
				[
					// Make it an anonymous user that can be migrated to actor
					$userIDField => 0,
					$userNameField => $newUserName
				],
				[
					$userNameField => $oldUserName
				],
				__METHOD__
			);

			if ( $success ) {
				$this->output( " -> DONE\n" );
			} else {
				$this->output( " -> FAILED\n" );
			}
		}
	}

}

$maintClass = PrepareActorMigration2::class;
require_once RUN_MAINTENANCE_IF_MAIN;
