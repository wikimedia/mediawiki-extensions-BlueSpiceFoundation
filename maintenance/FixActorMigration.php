<?php

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
		$this->outputUnknown();
		$this->fixData();
	}

	private function setActorId() {
		$user = User::newFromName( $this->unknownUserName );
		$this->actorId = $user->getActorId();
	}

	private function fetchAllTempActorRevs() {
		$res = $this->db->select( 'revision_actor_temp', 'revactor_rev' );
		foreach ( $res as $row ) {
			$this->actorTempRevIds[] = (int)$row->revactor_rev;
		}
	}

	private function fetchAllRevisions() {
		$res = $this->db->select( 'revision', [ 'rev_id', 'rev_page', 'rev_timestamp' ] );
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
		$this->output( "Rev ids to be fixed: $missingTempActorRevIdsList\n" );
	}

	private function fixData() {
		foreach ( $this->missingTempActorRevIds as $revId ) {
			$data = $this->revisionData[$revId];
			$this->output( "." );

			$this->db->insert( 'revision_actor_temp', $data, __METHOD__ );
		}
	}
}

$maintClass = 'FixActorMigration';
require_once RUN_MAINTENANCE_IF_MAIN;
