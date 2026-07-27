<?php

namespace BlueSpice\Tests\HookHandler;

use BlueSpice\HookHandler\PermissionLockdown;
use BlueSpice\Utility\MaintenanceUser;
use BlueSpice\UtilityFactory;
use MediaWiki\Api\ApiBase;
use MediaWiki\Api\ApiQuery;
use MediaWiki\Api\ApiResult;
use MediaWiki\Config\HashConfig;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Title\Title;
use MediaWiki\Title\TitleFactory;
use MediaWiki\User\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BlueSpice\HookHandler\PermissionLockdown::onAPIAfterExecute
 */
class PermissionLockdownApiResultTest extends TestCase {

	/**
	 * @param array $queryResultData Data to pre-populate under ['query'] in the ApiResult
	 * @param array $readablePages Map of prefixed title => bool (true = user can read)
	 * @param array $expectedQueryData Expected ['query'] result after filtering
	 *
	 * @covers \BlueSpice\HookHandler\PermissionLockdown::onAPIAfterExecute
	 * @dataProvider provideFilteringTestData
	 */
	public function testOnAPIAfterExecute(
		array $queryResultData,
		array $readablePages,
		array $expectedQueryData
	) {
		$handler = $this->makeHandler( $readablePages );

		$result = new ApiResult( false );
		foreach ( $queryResultData as $key => $value ) {
			$result->addValue( 'query', $key, $value );
		}

		$module = $this->createMock( ApiQuery::class );
		$module->method( 'getResult' )->willReturn( $result );

		$handler->onAPIAfterExecute( $module );

		$actualQueryData = $result->getResultData( [ 'query' ] );
		$actualQueryData = $this->stripMetadata( $actualQueryData );
		$expectedQueryData = $this->stripMetadata( $expectedQueryData );

		$this->assertEquals( $expectedQueryData, $actualQueryData );
	}

	public function provideFilteringTestData(): array {
		return [
			'allpages - removes inaccessible pages' => [
				'queryResultData' => [
					'allpages' => [
						[ 'pageid' => 1, 'ns' => 0, 'title' => 'Accessible Page' ],
						[ 'pageid' => 2, 'ns' => 0, 'title' => 'Secret Page' ],
						[ 'pageid' => 3, 'ns' => 0, 'title' => 'Another Accessible' ],
					],
				],
				'readablePages' => [
					'Accessible Page' => true,
					'Secret Page' => false,
					'Another Accessible' => true,
				],
				'expectedQueryData' => [
					'allpages' => [
						[ 'pageid' => 1, 'ns' => 0, 'title' => 'Accessible Page' ],
						[ 'pageid' => 3, 'ns' => 0, 'title' => 'Another Accessible' ],
					],
				],
			],
			'allpages - no change when all accessible' => [
				'queryResultData' => [
					'allpages' => [
						[ 'pageid' => 1, 'ns' => 0, 'title' => 'Page One' ],
						[ 'pageid' => 2, 'ns' => 0, 'title' => 'Page Two' ],
					],
				],
				'readablePages' => [
					'Page One' => true,
					'Page Two' => true,
				],
				'expectedQueryData' => [
					'allpages' => [
						[ 'pageid' => 1, 'ns' => 0, 'title' => 'Page One' ],
						[ 'pageid' => 2, 'ns' => 0, 'title' => 'Page Two' ],
					],
				],
			],
			'allpages - removes all pages when none accessible' => [
				'queryResultData' => [
					'allpages' => [
						[ 'pageid' => 1, 'ns' => 0, 'title' => 'Secret1' ],
						[ 'pageid' => 2, 'ns' => 0, 'title' => 'Secret2' ],
					],
				],
				'readablePages' => [
					'Secret1' => false,
					'Secret2' => false,
				],
				'expectedQueryData' => [
					'allpages' => [],
				],
			],
			'recentchanges - filters by title' => [
				'queryResultData' => [
					'recentchanges' => [
						[ 'type' => 'edit', 'ns' => 0, 'title' => 'Public Edit' ],
						[ 'type' => 'edit', 'ns' => 0, 'title' => 'Secret Edit' ],
					],
				],
				'readablePages' => [
					'Public Edit' => true,
					'Secret Edit' => false,
				],
				'expectedQueryData' => [
					'recentchanges' => [
						[ 'type' => 'edit', 'ns' => 0, 'title' => 'Public Edit' ],
					],
				],
			],
			'categorymembers - filters by title' => [
				'queryResultData' => [
					'categorymembers' => [
						[ 'pageid' => 10, 'ns' => 0, 'title' => 'Allowed Member' ],
						[ 'pageid' => 20, 'ns' => 0, 'title' => 'Forbidden Member' ],
						[ 'pageid' => 30, 'ns' => 0, 'title' => 'Another Allowed' ],
					],
				],
				'readablePages' => [
					'Allowed Member' => true,
					'Forbidden Member' => false,
					'Another Allowed' => true,
				],
				'expectedQueryData' => [
					'categorymembers' => [
						[ 'pageid' => 10, 'ns' => 0, 'title' => 'Allowed Member' ],
						[ 'pageid' => 30, 'ns' => 0, 'title' => 'Another Allowed' ],
					],
				],
			],
			'search - filters and adjusts totalhits' => [
				'queryResultData' => [
					'searchinfo' => [ 'totalhits' => 100 ],
					'search' => [
						[ 'ns' => 0, 'title' => 'Found Public', 'snippet' => '...' ],
						[ 'ns' => 0, 'title' => 'Found Secret', 'snippet' => '...' ],
						[ 'ns' => 0, 'title' => 'Found Also Public', 'snippet' => '...' ],
					],
				],
				'readablePages' => [
					'Found Public' => true,
					'Found Secret' => false,
					'Found Also Public' => true,
				],
				'expectedQueryData' => [
					'searchinfo' => [ 'totalhits' => 99 ],
					'search' => [
						[ 'ns' => 0, 'title' => 'Found Public', 'snippet' => '...' ],
						[ 'ns' => 0, 'title' => 'Found Also Public', 'snippet' => '...' ],
					],
				],
			],
			'multiple modules in one response' => [
				'queryResultData' => [
					'allpages' => [
						[ 'pageid' => 1, 'ns' => 0, 'title' => 'Public' ],
						[ 'pageid' => 2, 'ns' => 0, 'title' => 'Private' ],
					],
					'recentchanges' => [
						[ 'type' => 'new', 'ns' => 0, 'title' => 'Public' ],
						[ 'type' => 'new', 'ns' => 0, 'title' => 'Private' ],
					],
				],
				'readablePages' => [
					'Public' => true,
					'Private' => false,
				],
				'expectedQueryData' => [
					'allpages' => [
						[ 'pageid' => 1, 'ns' => 0, 'title' => 'Public' ],
					],
					'recentchanges' => [
						[ 'type' => 'new', 'ns' => 0, 'title' => 'Public' ],
					],
				],
			],
			'items without title field are preserved' => [
				'queryResultData' => [
					'allpages' => [
						[ 'pageid' => 1, 'ns' => 0, 'title' => 'Visible' ],
						[ 'pageid' => 2, 'some_other_field' => 'no title here' ],
						[ 'pageid' => 3, 'ns' => 0, 'title' => 'Hidden' ],
					],
				],
				'readablePages' => [
					'Visible' => true,
					'Hidden' => false,
				],
				'expectedQueryData' => [
					'allpages' => [
						[ 'pageid' => 1, 'ns' => 0, 'title' => 'Visible' ],
						[ 'pageid' => 2, 'some_other_field' => 'no title here' ],
					],
				],
			],
			'unknown module is not filtered' => [
				'queryResultData' => [
					'siteinfo' => [
						[ 'sitename' => 'Test Wiki' ],
					],
				],
				'readablePages' => [],
				'expectedQueryData' => [
					'siteinfo' => [
						[ 'sitename' => 'Test Wiki' ],
					],
				],
			],
			'namespace-aware title resolution' => [
				'queryResultData' => [
					'allpages' => [
						[ 'pageid' => 1, 'ns' => 2, 'title' => 'SomeUser' ],
						[ 'pageid' => 2, 'ns' => 0, 'title' => 'MainPage' ],
					],
				],
				'readablePages' => [
					'User:SomeUser' => false,
					'MainPage' => true,
				],
				'expectedQueryData' => [
					'allpages' => [
						[ 'pageid' => 2, 'ns' => 0, 'title' => 'MainPage' ],
					],
				],
			],
		];
	}

	/**
	 * @covers \BlueSpice\HookHandler\PermissionLockdown::onAPIAfterExecute
	 */
	public function testSkipsNonApiQueryModules() {
		$permissionManager = $this->createMock( PermissionManager::class );
		$permissionManager->expects( $this->never() )->method( 'userCan' );

		$handler = new PermissionLockdown(
			new HashConfig( [ 'CommandLineMode' => false ] ),
			$permissionManager,
			$this->createMock( TitleFactory::class ),
			$this->createMock( RevisionLookup::class ),
			$this->createMock( UtilityFactory::class ),
			false
		);

		$module = $this->createMock( ApiBase::class );
		$handler->onAPIAfterExecute( $module );

		// If we get here without userCan being called, the test passes
		$this->assertTrue( true );
	}

	/**
	 * @covers \BlueSpice\HookHandler\PermissionLockdown::onAPIAfterExecute
	 */
	public function testSearchTotalHitsDoesNotGoBelowZero() {
		$handler = $this->makeHandler( [
			'Secret1' => false,
			'Secret2' => false,
			'Secret3' => false,
		] );

		$result = new ApiResult( false );
		$result->addValue( 'query', 'searchinfo', [ 'totalhits' => 1 ] );
		$result->addValue( 'query', 'search', [
			[ 'ns' => 0, 'title' => 'Secret1' ],
			[ 'ns' => 0, 'title' => 'Secret2' ],
			[ 'ns' => 0, 'title' => 'Secret3' ],
		] );

		$module = $this->createMock( ApiQuery::class );
		$module->method( 'getResult' )->willReturn( $result );

		$handler->onAPIAfterExecute( $module );

		$searchInfo = $result->getResultData( [ 'query', 'searchinfo' ] );
		$this->assertSame( 0, $searchInfo['totalhits'] );
	}

	/**
	 * @covers \BlueSpice\HookHandler\PermissionLockdown::onAPIAfterExecute
	 */
	public function testUsesMaintenanceUserInCliContext() {
		$maintenanceUserMock = $this->createMock( User::class );
		$maintenanceUserMock->method( 'getName' )->willReturn( 'BSMaintenance' );
		$maintenanceUserMock->method( 'isRegistered' )->willReturn( true );

		$permissionManager = $this->createMock( PermissionManager::class );
		$permissionManager->method( 'userCan' )->willReturnCallback(
			static function ( $action, $user, $title ) {
				if ( $user->getName() === 'BSMaintenance' ) {
					return true;
				}
				return false;
			}
		);

		$titleFactory = $this->createMock( TitleFactory::class );
		$titleFactory->method( 'makeTitle' )->willReturnCallback(
			function ( int $ns, string $titleText ) {
				return $this->makeMockTitle( $ns, $titleText );
			}
		);

		$maintenanceUser = $this->createMock( MaintenanceUser::class );
		$maintenanceUser->method( 'getUser' )->willReturn( $maintenanceUserMock );
		$utilityFactory = $this->createMock( UtilityFactory::class );
		$utilityFactory->method( 'getMaintenanceUser' )->willReturn( $maintenanceUser );

		$handler = new PermissionLockdown(
			new HashConfig( [ 'CommandLineMode' => true ] ),
			$permissionManager,
			$titleFactory,
			$this->createMock( RevisionLookup::class ),
			$utilityFactory,
			false
		);

		$result = new ApiResult( false );
		$result->addValue( 'query', 'allpages', [
			[ 'pageid' => 1, 'ns' => 0, 'title' => 'SomePage' ],
		] );

		$module = $this->createMock( ApiQuery::class );
		$module->method( 'getResult' )->willReturn( $result );

		$handler->onAPIAfterExecute( $module );

		$queryData = $result->getResultData( [ 'query', 'allpages' ] );
		$queryData = $this->stripMetadata( $queryData );
		$this->assertCount( 1, $queryData );
		$this->assertEquals( 'SomePage', $queryData[0]['title'] );
	}

	/**
	 * @covers \BlueSpice\HookHandler\PermissionLockdown::onAPIAfterExecute
	 */
	public function testEmptyQueryDataIsSkipped() {
		$permissionManager = $this->createMock( PermissionManager::class );
		$permissionManager->expects( $this->never() )->method( 'userCan' );

		$handler = new PermissionLockdown(
			new HashConfig( [ 'CommandLineMode' => false ] ),
			$permissionManager,
			$this->createMock( TitleFactory::class ),
			$this->createMock( RevisionLookup::class ),
			$this->createMock( UtilityFactory::class ),
			false
		);

		$result = new ApiResult( false );
		$module = $this->createMock( ApiQuery::class );
		$module->method( 'getResult' )->willReturn( $result );

		$handler->onAPIAfterExecute( $module );

		$this->assertNull( $result->getResultData( [ 'query' ] ) );
	}

	/**
	 * Create a PermissionLockdown handler wired for API result filtering tests.
	 *
	 * @param array $readablePages Map of prefixed title => bool
	 * @return PermissionLockdown
	 */
	private function makeHandler( array $readablePages ): PermissionLockdown {
		$config = new HashConfig( [ 'CommandLineMode' => false ] );

		$permissionManager = $this->createMock( PermissionManager::class );
		$permissionManager->method( 'userCan' )->willReturnCallback(
			static function ( $action, $user, $title ) use ( $readablePages ) {
				return $readablePages[$title->getPrefixedText()] ?? true;
			}
		);

		$titleFactory = $this->createMock( TitleFactory::class );
		$titleFactory->method( 'makeTitle' )->willReturnCallback(
			function ( int $ns, string $titleText ) {
				return $this->makeMockTitle( $ns, $titleText );
			}
		);
		$titleFactory->method( 'newFromText' )->willReturnCallback(
			function ( string $titleText ) {
				return $this->makeMockTitle( NS_MAIN, $titleText );
			}
		);

		$utilityFactory = $this->createMock( UtilityFactory::class );

		return new PermissionLockdown(
			$config,
			$permissionManager,
			$titleFactory,
			$this->createMock( RevisionLookup::class ),
			$utilityFactory,
			false
		);
	}

	/**
	 * Create a mock Title with the given namespace and title text.
	 *
	 * @param int $ns
	 * @param string $titleText
	 * @return Title
	 */
	private function makeMockTitle( int $ns, string $titleText ): Title {
		$nsNames = [
			0 => '',
			1 => 'Talk',
			2 => 'User',
			3 => 'User_talk',
			6 => 'File',
		];

		$prefix = isset( $nsNames[$ns] ) && $nsNames[$ns] !== ''
			? $nsNames[$ns] . ':' : '';

		$title = $this->createMock( Title::class );
		$title->method( 'getNamespace' )->willReturn( $ns );
		$title->method( 'getText' )->willReturn( $titleText );
		$title->method( 'getDBkey' )->willReturn( str_replace( ' ', '_', $titleText ) );
		$title->method( 'getPrefixedText' )->willReturn( $prefix . $titleText );
		return $title;
	}

	/**
	 * Recursively strip ApiResult metadata keys (keys starting with '_').
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	private function stripMetadata( $data ) {
		if ( !is_array( $data ) ) {
			return $data;
		}
		$cleaned = [];
		foreach ( $data as $key => $value ) {
			if ( is_string( $key ) && str_starts_with( $key, '_' ) ) {
				continue;
			}
			$cleaned[$key] = $this->stripMetadata( $value );
		}
		if ( array_is_list( $cleaned ) ) {
			$cleaned = array_values( $cleaned );
		}
		return $cleaned;
	}
}
