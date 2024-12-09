<?php

namespace BlueSpice\Tests\HookHandler;

use BlueSpice\HookHandler\PermissionLockdown;
use BlueSpice\Utility\MaintenanceUser;
use BlueSpice\UtilityFactory;
use HashConfig;
use MediaWiki\Linker\LinkTarget;
use MediaWiki\Permissions\PermissionManager;
use PHPUnit\Framework\TestCase;
use User;

class PermissionLockdownTest extends TestCase {
	/**
	 * @param bool $cliContext
	 * @param bool $noSession
	 * @param bool $expectedSkip
	 * @param bool $userCanReturn
	 * @param bool $userCanReturnForMaintenanceuser
	 *
	 * @covers \BlueSpice\HookHandler\PermissionLockdown::__construct
	 * @covers \BlueSpice\HookHandler\PermissionLockdown::onBeforeParserFetchTemplateRevisionRecord
	 *
	 * @dataProvider provideOnBeforeParserFetchTemplateRevisionRecordTestData
	 */
	public function testOnBeforeParserFetchTemplateRevisionRecord( $cliContext, $noSession, $expectedSkip,
		$userCanReturn, $userCanReturnForMaintenanceuser ) {
		$config = new HashConfig( [
			'CommandLineMode' => $cliContext
		] );

		$permissionManager = $this->createMock( PermissionManager::class );
		$permissionManager->method( 'userCan' )->willReturnCallback(
			static function ( $action, $user, $title ) use ( $userCanReturn, $userCanReturnForMaintenanceuser ) {
				if ( $user->getName() === 'Maintenance' ) {
					return $userCanReturnForMaintenanceuser;
				}
				return $userCanReturn;
			} );

		$utilityFactory = $this->createMock( UtilityFactory::class );
		$maintenanceUser = $this->createMock( MaintenanceUser::class );
		$maintenanceUserUser = $this->createMock( User::class );
		$maintenanceUserUser->method( 'getName' )->willReturn( 'Maintenance' );
		$maintenanceUser->method( 'getUser' )->willReturn( $maintenanceUserUser );
		$utilityFactory->method( 'getMaintenanceUser' )->willReturn( $maintenanceUser );

		$handler = new PermissionLockdown( $config, $permissionManager, $utilityFactory, $noSession );

		$contextTitle = null;
		$title = $this->createMock( LinkTarget::class );
		$skip = false;
		$revRecord = null;

		$handler->onBeforeParserFetchTemplateRevisionRecord( $contextTitle, $title, $skip, $revRecord );

		$this->assertEquals( $expectedSkip, $skip );
	}

	public function provideOnBeforeParserFetchTemplateRevisionRecordTestData(): array {
		return [
			'not-allowed-but-cli-context' => [
				'cliContext' => true,
				'noSession' => false,
				'expectedSkip' => false,
				'userCanReturn' => false,
				'userCanReturnForMaintenanceuser' => true
			],
			'not-allowed-but-no-session-context' => [
				'cliContext' => true,
				'noSession' => true,
				'expectedSkip' => false,
				'userCanReturn' => false,
				'userCanReturnForMaintenanceuser' => true
			],
			'regular-not-allowed' => [
				'cliContext' => false,
				'noSession' => false,
				'expectedSkip' => true,
				'userCanReturn' => false,
				'userCanReturnForMaintenanceuser' => true
			],
			'regular-allowed' => [
				'cliContext' => false,
				'noSession' => false,
				'expectedSkip' => false,
				'userCanReturn' => true,
				'userCanReturnForMaintenanceuser' => true
			]
		];
	}
}
