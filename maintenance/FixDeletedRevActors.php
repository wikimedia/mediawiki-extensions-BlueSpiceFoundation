<?php

use MediaWiki\Maintenance\Maintenance;
use MediaWiki\MediaWikiServices;

require_once 'BSMaintenance.php';

/**
 * This script fixes cases when revision has "rev_actor=0" in "revision" table.
 *
 * It may happen if wiki is old enough, because in older MediaWiki versions users
 * could be deleted (so actually removed from DB).
 * Then, if some revision has "rev_actor=0" - then most likely is that user who created this revision -
 * - was deleted at some point.
 *
 * Script assigns such revisions to some existing user. By default, to "BSMaintenance"
 *
 * Warning!
 * Please make sure that "actor migration" was done BEFORE executing this script,
 * otherwise "rev_actor" field may not exist.
 */
class FixDeletedRevActors extends Maintenance {

	/**
	 * @var IMaintainableDatabase
	 */
	private $db;

	/**
	 * @var int
	 */
	private $actorId;

	/**
	 * @var string
	 */
	private $deletedUserName;

	public function __construct() {
		parent::__construct();
		$this->addOption( 'deletedUserName', 'The name of user revisions should be assigned to. ' .
			'By default: BSMaintenance', false, true );
	}

	/**
	 * @inheritDoc
	 */
	public function execute() {
		$this->deletedUserName = $this->getOption( 'deletedUserName', 'BSMaintenance' );
		$this->db = $this->getDB( DB_PRIMARY );

		$this->setActorId();
		$this->fixRevActors();
	}

	private function setActorId() {
		$user = MediaWikiServices::getInstance()->getUserFactory()
			->newFromName( $this->deletedUserName );

		if ( !$user->isRegistered() ) {
			$this->output( 'User with such name does not exist!' );
			exit;
		}

		$this->actorId = $user->getActorId();
	}

	private function fixRevActors() {
		$this->db->update(
			'revision',
			[
				'rev_actor' => $this->actorId
			],
			[
				'rev_actor' => 0
			]
		);

		$numRecords = $this->db->affectedRows();
		$this->output( "$numRecords records fixed." );
	}
}

$maintClass = FixDeletedRevActors::class;
require_once RUN_MAINTENANCE_IF_MAIN;
