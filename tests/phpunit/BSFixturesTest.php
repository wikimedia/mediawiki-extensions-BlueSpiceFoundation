<?php

namespace BlueSpice\Tests;

use MediaWiki\MediaWikiServices;

/**
 * @group medium
 * @group Database
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class BSFixturesTest extends BSApiTestCase {

	public function setUp() : void {
		parent::setUp();
		new BSPageFixtures( $this );
		new BSUserFixtures( $this );
	}

	/**
	 * @covers \Title::newFromText
	 */
	public function testPageFixtures() {
		$title = \Title::newFromText( 'Template:Hello World' );
		$this->assertTrue( $title->exists(), 'Title should be known' );
	}

	/**
	 * @covers \User::newFromName
	 */
	public function testUserFixtures() {
		$user = \User::newFromName( 'Paul' );
		$this->assertFalse( $user->isAnon(), "User should be known" );

		$groups = MediaWikiServices::getInstance()
			->getUserGroupManager()
			->getUserGroups( $user );

		$this->assertContains( 'A', $groups, 'User should be in group A' );
		$this->assertNotContains( 'B', $groups, 'User should not be in group B' );
		$this->assertContains( 'C', $groups, 'User should be in group C' );
	}
}
