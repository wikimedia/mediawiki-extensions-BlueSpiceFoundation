<?php

namespace BlueSpice\Tests\HookHandler;

use ApiBase;
use BlueSpice\HookHandler\PermissionLockdown;
use BlueSpice\Utility\MaintenanceUser;
use BlueSpice\UtilityFactory;
use HashConfig;
use MediaWiki\Linker\LinkTarget;
use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Revision\RevisionRecord;
use PHPUnit\Framework\TestCase;
use TitleFactory;
use User;
use WebRequest;

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

		$titleFactory = $this->createMock( TitleFactory::class );
		$revisionLookup = $this->createMock( RevisionLookup::class );
		$utilityFactory = $this->createMock( UtilityFactory::class );
		$maintenanceUser = $this->createMock( MaintenanceUser::class );
		$maintenanceUserUser = $this->createMock( User::class );
		$maintenanceUserUser->method( 'getName' )->willReturn( 'Maintenance' );
		$maintenanceUser->method( 'getUser' )->willReturn( $maintenanceUserUser );
		$utilityFactory->method( 'getMaintenanceUser' )->willReturn( $maintenanceUser );

		$handler = new PermissionLockdown(
			 $config,
			 $permissionManager,
			 $titleFactory,
			 $revisionLookup,
			 $utilityFactory,
			 $noSession
		);

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

	/**
	 * @param array $params
	 * @param array $userCanRead
	 * @param array $expectedModifiedParams
	 *
	 * @covers \BlueSpice\HookHandler\PermissionLockdown::onApiCheckCanExecute
	 *
	 * @dataProvider provideOnApiCheckCanExecuteTestData
	 */
	public function testOnApiCheckCanExecute( $params, $userCanRead, $expectedModifiedParams ) {
		$config = new HashConfig( [] );
		$permissionManager = $this->createMock( PermissionManager::class );
		$permissionManager->method( 'userCan' )->willReturnCallback(
			static function ( $action, $user, $title ) use ( $userCanRead ) {
				return $userCanRead[$title->getPrefixedText()] ?? false;
			} );
		$titleFactoryReal = MediaWikiServices::getInstance()->getTitleFactory();
		$titleFactory = $this->createMock( TitleFactory::class );
		$titleFactory->method( 'newFromText' )->willReturnCallback(
			static function ( $text ) use ( $titleFactoryReal ) {
				$title = $titleFactoryReal->newFromText( $text );
				return $title;
			} );
		$titleFactory->method( 'makeTitle' )->willReturnCallback(
			static function ( $nsId, $dbKey ) use ( $titleFactoryReal ) {
				$title = $titleFactoryReal->makeTitle( $nsId, $dbKey );
				return $title;
			} );
		$titleFactory->method( 'newFromID' )->willReturnCallback(
			static function ( $id ) use ( $titleFactoryReal ) {
				// For the sake of this test, we align the page name with the page ID
				$pageName = "Page$id";
				$title = $titleFactoryReal->newFromText( $pageName );
				return $title;
			} );
		$revisionLookup = $this->createMock( RevisionLookup::class );
		$me = $this;
		$revisionLookup->method( 'getRevisionById' )->willReturnCallback(
			static function ( $id ) use ( $me ) {
				$revision = $me->createMock( RevisionRecord::class );
				$revision->method( 'getPage' )->willReturnCallback(
					static function () use ( $id ) {
						// For the sake of this test, we align the page name with the revision ID
						$pageName = "Page$id";
						$title = MediaWikiServices::getInstance()->getTitleFactory()->newFromText( $pageName );
						return $title;
					} );
				return $revision;
			} );
		$utilityFactory = $this->createMock( UtilityFactory::class );
		$noSession = false;
		$handler = new PermissionLockdown(
			$config,
			$permissionManager,
			$titleFactory,
			$revisionLookup,
			$utilityFactory,
			$noSession
		);

		$module = $this->createMock( ApiBase::class );
		$user = $this->createMock( User::class );
		$request = $this->createMock( WebRequest::class );
		$request->method( 'getVal' )->willReturnCallback(
			static function ( $key ) use ( &$params ) {
				return $params[$key] ?? null;
			} );
		$request->method( 'setVal' )->willReturnCallback(
			static function ( $key, $value ) use ( &$params ) {
				$params[$key] = $value;
			} );
		$module->method( 'getUser' )->willReturn( $user );
		$module->method( 'getRequest' )->willReturn( $request );

		$handler->onApiBeforeMain( $module );
		$this->assertEquals( $expectedModifiedParams, $params );
	}

	public function provideOnApiCheckCanExecuteTestData(): array {
		return [
			'titles-changed' => [
				'params' => [
					'action' => 'query',
					'format' => 'json',
					'prop' => 'info|extracts|pageimages|revisions|info',
					'formatversion' => '2',
					'redirects' => 'true',
					'exchars' => '525',
					'explaintext' => 'true',
					'exsectionformat' => 'plain',
					'piprop' => 'thumbnail',
					'pithumbsize' => '480',
					'pilicense' => 'any',
					'rvprop' => 'timestamp',
					'inprop' => 'url',
					'titles' => 'Page1|Page2',
					'smaxage' => '300',
					'maxage' => '300',
					'uselang' => 'content',
				],
				'userCanRead' => [
					'Page1' => false,
					'Page2' => true
				],
				'expectedModifiedParams' => [
					'action' => 'query',
					'format' => 'json',
					'prop' => 'info|extracts|pageimages|revisions|info',
					'formatversion' => '2',
					'redirects' => 'true',
					'exchars' => '525',
					'explaintext' => 'true',
					'exsectionformat' => 'plain',
					'piprop' => 'thumbnail',
					'pithumbsize' => '480',
					'pilicense' => 'any',
					'rvprop' => 'timestamp',
					'inprop' => 'url',
					'titles' => 'Page2',
					'smaxage' => '300',
					'maxage' => '300',
					'uselang' => 'content',
				],
			],
			'titles-unchanged' => [
				'params' => [
					'action' => 'query',
					'format' => 'json',
					'prop' => 'info|extracts|pageimages|revisions|info',
					'formatversion' => '2',
					'redirects' => 'true',
					'exchars' => '525',
					'explaintext' => 'true',
					'exsectionformat' => 'plain',
					'piprop' => 'thumbnail',
					'pithumbsize' => '480',
					'pilicense' => 'any',
					'rvprop' => 'timestamp',
					'inprop' => 'url',
					'titles' => 'Page1|Page2',
					'smaxage' => '300',
					'maxage' => '300',
					'uselang' => 'content',
				],
				'userCanRead' => [
					'Page1' => true,
					'Page2' => true
				],
				'expectedModifiedParams' => [
					'action' => 'query',
					'format' => 'json',
					'prop' => 'info|extracts|pageimages|revisions|info',
					'formatversion' => '2',
					'redirects' => 'true',
					'exchars' => '525',
					'explaintext' => 'true',
					'exsectionformat' => 'plain',
					'piprop' => 'thumbnail',
					'pithumbsize' => '480',
					'pilicense' => 'any',
					'rvprop' => 'timestamp',
					'inprop' => 'url',
					'titles' => 'Page1|Page2',
					'smaxage' => '300',
					'maxage' => '300',
					'uselang' => 'content',
				],
			],
			'revids-changed' => [
				'params' => [
					'revids' => '1|2',
				],
				'userCanRead' => [
					'Page1' => false,
					'Page2' => true
				],
				'expectedModifiedParams' => [
					'revids' => '2',
				],
			],
			'revids-unchanged' => [
				'params' => [
					'revids' => '1|2',
				],
				'userCanRead' => [
					'Page1' => true,
					'Page2' => true
				],
				'expectedModifiedParams' => [
					'revids' => '1|2',
				],
			],
			'pageids-changed' => [
				'params' => [
					'pageids' => '1|2',
				],
				'userCanRead' => [
					'Page1' => false,
					'Page2' => true
				],
				'expectedModifiedParams' => [
					'pageids' => '2',
				],
			],
			'pageids-unchanged' => [
				'params' => [
					'pageids' => '1|2',
				],
				'userCanRead' => [
					'Page1' => true,
					'Page2' => true
				],
				'expectedModifiedParams' => [
					'pageids' => '1|2',
				],
			]
		];
	}
}
