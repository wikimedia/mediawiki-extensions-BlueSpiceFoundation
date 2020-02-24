<?php

namespace BlueSpice\Tests\Permission\Lockdown;

use BlueSpice\Permission\Lockdown\Module\Namespaces;
use BlueSpice\Services;
use MediaWiki\MediaWikiServices;
use MediaWikiTestCase;
use Title;
use User;

/**
 * @covers \BlueSpice\Permission\Lockdown\Module\Namespaces
 */
class NamespaceLockdownTest extends MediaWikiTestCase {
	/** @var Namespaces */
	protected $module;
	/** @var User $user */
	protected $user;

	protected function setUp() : void {
		parent::setUp();

		$user = $this->getTestUser()->getUser();
		// Strip to just "user" group
		array_map( function ( $group ) use ( $user ) {
			$user->removeGroup( $group );
		}, $user->getGroups() );
		$this->user = $user;
	}

	public function provideTitlesArguments() {
		return [
			[ Title::newMainPage(), true ],
			[ Title::makeTitle( NS_CATEGORY, 'Dummy' ), true ],
			[ Title::makeTitle( NS_HELP, 'Dummy' ),true ],
			[ Title::makeTitle( NS_SPECIAL, 'Dummy' ), false ],
		];
	}

	/** @dataProvider provideTitlesArguments */
	public function testLocking( Title $title, $shouldApply ) {
		$this->setModule();
		$this->setMwGlobals( 'bsgNamespaceRolesLockdown', [] );
		$mwPermissionManager = MediaWikiServices::getInstance()->getPermissionManager();

		$appliedNotSet = $this->module->applies( $title, $this->user );
		$this->setMwGlobals( 'bsgNamespaceRolesLockdown', [
			$title->getNamespace() => [ 'reader' => [ 'user' ] ]
		] );
		$appliedSet = $this->module->applies( $title, $this->user );
		if ( $shouldApply ) {
			$this->assertTrue(
				!$appliedNotSet && $appliedSet,
				'Module should apply to ' . $title->getPrefixedDBkey()
			);
		} else {
			$this->assertTrue(
				!$appliedNotSet && !$appliedSet,
				'Module should not apply to ' . $title->getPrefixedDBkey()
			);
		}

		if ( !$appliedSet ) {
			$this->markTestSkipped( 'Module does not apply to ' . $title->getPrefixedDBkey() );
			return;
		}

		$this->assertTrue(
			$mwPermissionManager->userCan( 'read', $this->user, $title ),
			'Users in "user" group should be able to read ' .
			$title->getPrefixedDBkey() . ' when its locked down to "user" group'
		);
		$this->setMwGlobals( 'bsgNamespaceRolesLockdown', [
			$title->getNamespace() => [ 'reader' => [ 'sysop' ] ]
		] );
		$this->assertFalse(
			$mwPermissionManager->userCan( 'read', $this->user, $title ),
			'Users in "user" group should not be able to read ' .
			$title->getPrefixedDBkey() . ' when its locked down to "sysop" group'
		);
	}

	protected function setModule() {
		/** @var Services $services */
		$services = Services::getInstance();
		$this->module = Namespaces::getInstance(
			$services->getConfigFactory()->makeConfig( 'bsg' ),
			\RequestContext::getMain(),
			$services,
			$services->getService( 'BSRoleManager' )
		);
	}
}
