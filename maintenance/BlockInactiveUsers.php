<?php

use MediaWiki\Block\DatabaseBlock;
use MediaWiki\Block\DatabaseBlockStore;

require_once __DIR__ . '/BSMaintenance.php';

class BlockInactiveUsers extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->addOption( 'threshold', 'Block users not active for this number of days' );
	}

	/**
	 * @inheritDoc
	 */
	public function execute() {
		$databaseBlockStore = $this->getServiceContainer()->getDatabaseBlockStore();
		$loadBalancer = $this->getServiceContainer()->getDBLoadBalancer();
		$threshold = $this->getOption( 'threshold' ) ?? 180;
		$thresholdTime = strtotime( "-$threshold days" );
		$this->output( "Blocking users not active for $threshold days.\n" );

		$whitelist = $this->getServiceContainer()->getMainConfig()->get( 'ReservedUsernames' ) ?? [];
		$dbr = $loadBalancer->getConnection( DB_REPLICA );
		$res = $dbr->newSelectQueryBuilder()
			->table( 'user' )
			->fields( '*' )
			->leftJoin(
				'block_target',
				null,
				'bt_user = user_id'
			)
			->where( [
				// not blocked
				'bt_user' => null,
				// inactive
				"user_touched < {$dbr->addQuotes( $dbr->timestamp( $thresholdTime ) )}",
				// not whitelisted
				"user_name NOT IN ({$dbr->makeList( $whitelist )})"
			] )
			->caller( __METHOD__ )
			->fetchResultSet();
		$this->output( "Found {$res->numRows()} inactive users.\n" );

		foreach ( $res as $row ) {
			$user = $this->getServiceContainer()->getUserFactory()->newFromRow( $row );
			$this->blockUser( $user, $databaseBlockStore );
		}
	}

	/**
	 * @param User $user
	 * @param DatabaseBlockStore $databaseBlockStore
	 */
	private function blockUser( User $user, DatabaseBlockStore $databaseBlockStore ) {
		$block = new DatabaseBlock();
		$block->setBlocker( $this->getActor() );
		$block->setTarget( $user );
		$block->setExpiry( 'infinity' );
		$block->setReason( 'Blocked due to inactivity' );
		$block->isEmailBlocked( true );
		$block->isCreateAccountBlocked( false );
		$block->isUsertalkEditAllowed( true );
		$block->isHardblock( true );
		$block->isAutoblocking( false );
		$res = $databaseBlockStore->insertBlock( $block );
		if ( !$res ) {
			$this->error( "Tried to disable user {$user->getName()}, failed.\n" );
		} else {
			$this->output( "Disabled user {$user->getName()}.\n" );
		}
	}

	/**
	 * @return User
	 */
	private function getActor() {
		return User::newSystemUser( 'MediaWiki default', [ 'steal' => true ] );
	}
}

$maintClass = 'BlockInactiveUsers';
require_once RUN_MAINTENANCE_IF_MAIN;
