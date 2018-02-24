<?php

namespace BlueSpice\Tests\DataSources;

/**
 * @group BlueSpice
 * @group BlueSpiceFoundation
 * @group Database
 * @group heavy
 */
class WatchlistTest extends \MediaWikiTestCase {

	protected $tablesUsed = [ 'watchlist', 'user' ];

	public function addDBData() {

		$this->insertPage( 'Test A' );
		$this->insertPage( 'Talk:Test A' );
		$this->insertPage( 'Test B' );
		$this->insertPage( 'Talk:Test B' );
		$this->insertPage( 'Test C' );
		$this->insertPage( 'Test D' );

		$dummyDbEntries = [
			[ 1, 0, 'Test A', null ],
			[ 1, 1, 'Test A', null ],
			[ 1, 0, 'Test B', null ],
			[ 1, 1, 'Test B', '19700101000000' ],
			[ 1, 0, 'Test C', null ],
			[ 1, 0, 'Test D', null ],
			[ 2, 0, 'Test A', null ],
			[ 2, 1, 'Test A', null ],
			[ 2, 0, 'Test B', null ]
		];

		$dbw = wfGetDB( DB_MASTER );
		foreach( $dummyDbEntries as $dummyDbEntry ) {
			$dbw->insert( 'watchlist', [
				'wl_user' => $dummyDbEntry[0],
				'wl_namespace' => $dummyDbEntry[1],
				'wl_title' => $dummyDbEntry[2],
				'wl_notificationtimestamp' => $dummyDbEntry[3],
			] );
		}

		$user = \User::newFromName( 'UTWatchlist' );
		if ( $user->getId() == 0 ) {
			$user->addToDatabase();
			\TestUser::setPasswordForUser( $user, 'UTWatchlist' );

			$user->saveSettings();
		}
	}

	public function testCanConstruct() {
		$loadBalancer = $this->getMockBuilder( '\Wikimedia\Rdbms\LoadBalancer' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\BlueSpice\Data\Watchlist\Reader',
			new \BlueSpice\Data\Watchlist\Reader(
				$loadBalancer
			)
		);
	}

	public function testUnfilteredFetching() {
		$watchlist = $this->makeInstance();
		$resultSet = $watchlist->read( new \BlueSpice\Data\ReaderParams() );

		$records = $resultSet->getRecords();
		$total = $resultSet->getTotal();

		$this->assertEquals( 9 , count( $records ), 'Count of datasets in result is wrong' );
		$this->assertEquals( 9 , $total, 'Count of total datasets is wrong' );
	}

	/**
	 *
	 * @return \BlueSpice\Data\Watchlist\Reader
	 */
	protected function makeInstance() {
		return new \BlueSpice\Data\Watchlist\Reader(
			\MediaWiki\MediaWikiServices::getInstance()->getDBLoadBalancer()
		);
	}
}
