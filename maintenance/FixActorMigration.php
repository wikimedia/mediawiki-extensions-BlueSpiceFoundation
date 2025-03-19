<?php

use MediaWiki\Maintenance\Maintenance;
use MediaWiki\MediaWikiServices;
use Wikimedia\Rdbms\IDatabase;

require_once 'BSMaintenance.php';

class FixActorMigration extends Maintenance {

	public function __construct() {
		parent::__construct();

		$this->addOption( 'unknownUserName', 'The Name that should be assigned', true, true );

		$this->requireExtension( 'BlueSpiceFoundation' );
	}

	/**
	 *
	 * @var IDatabase
	 */
	private $db = null;

	private $actorTempRevIds = [];
	private $revisionIds = [];
	private $revisionData = [];
	private $missingTempActorRevIds = [];
	private $actorId = -1;
	private $unknownUserName = '';

	public function execute() {
		$this->unknownUserName = $this->getOption( 'unknownUserName', 'WikiSysop' );
		$this->db = $this->getDB( DB_PRIMARY );
		$this->setActorId();
		$this->fetchAllTempActorRevs();
		$this->fetchAllRevisions();
		$this->missingTempActorRevIds = array_diff( $this->revisionIds, $this->actorTempRevIds );
		$this->fixData();
		$this->output( "\nDone\n" );
	}

	private function setActorId() {
		$user = MediaWikiServices::getInstance()->getUserFactory()
			->newFromName( $this->unknownUserName );
		$this->actorId = $user->getActorId();
	}

	private function fetchAllTempActorRevs() {
		$res = $this->db->select(
			'revision_actor_temp',
			'revactor_rev',
			'',
			__METHOD__
		);
		foreach ( $res as $row ) {
			$this->actorTempRevIds[] = (int)$row->revactor_rev;
		}
	}

	private function fetchAllRevisions() {
		$res = $this->db->select(
			'revision',
			[ 'rev_id', 'rev_page', 'rev_timestamp' ],
			'',
			__METHOD__
		);
		foreach ( $res as $row ) {
			$this->revisionIds[] = (int)$row->rev_id;
			$this->revisionData[(int)$row->rev_id] = [
				'revactor_rev' => (int)$row->rev_id,
				'revactor_actor' => $this->actorId,
				'revactor_timestamp' => $row->rev_timestamp,
				'revactor_page' => (int)$row->rev_page
			];
		}
	}

	private function outputUnknown() {
		$missingTempActorRevIdsList = implode( ', ', $this->missingTempActorRevIds );
		$this->output( "Rev ids to be fixed: $missingTempActorRevIdsList" );
	}

	private function fixData() {
		$this->output( "Fixing revision_actor_temp" );
		if ( count( $this->missingTempActorRevIds ) === 0 ) {
			$this->output( " - no bad entries found" );
		} else {
			$this->output( " - " . count( $this->missingTempActorRevIds ) . " bad entries found" );
			$this->outputUnknown();
		}
		foreach ( $this->missingTempActorRevIds as $revId ) {
			$data = $this->revisionData[$revId];
			$this->output( "." );

			$this->db->insert( 'revision_actor_temp', $data, __METHOD__ );
		}

		$this->fixAdditonalTables();
	}

	private $tablesToFix = [
		'archive' => 'ar_actor',
		// This script is meant for use in legacy version 1.35 only,
		// therefore the reference to the `ipblocks` table can remain
		'ipblocks' => 'ipb_by_actor',
		'image' => 'img_actor',
		'oldimage' => 'oi_actor',
		'filearchive' => 'fa_actor',
		'recentchanges' => 'rc_actor',
		'logging' => 'log_actor',
	];

	private function fixAdditonalTables() {
		foreach ( $this->tablesToFix as $tableName => $foreignKeyName ) {
			$this->output( "\nFixing $tableName" );
			$numberOfBadRows = $this->db->selectRowCount(
				$tableName,
				'*',
				[ $foreignKeyName => 0 ],
				__METHOD__
			);
			if ( $numberOfBadRows === 0 ) {
				$this->output( " - no bad entries found" );
				continue;
			}
			$this->db->update(
				$tableName,
				[ $foreignKeyName => $this->actorId ],
				[ $foreignKeyName => 0 ],
				__METHOD__
			);
			$this->output( " - $numberOfBadRows bad entries fixed" );
		}
	}
}

$maintClass = FixActorMigration::class;
require_once RUN_MAINTENANCE_IF_MAIN;
