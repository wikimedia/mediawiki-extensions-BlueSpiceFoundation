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

	public function setUp(): void {
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
		$services = MediaWikiServices::getInstance();
		$user = $services->getUserFactory()->newFromName( 'Paul' );
		$this->assertTrue( $user->isRegistered(), "User should be known" );

		$groups = $services->getUserGroupManager()->getUserGroups( $user );
		$this->assertContains( 'A', $groups, 'User should be in group A' );
		$this->assertNotContains( 'B', $groups, 'User should not be in group B' );
		$this->assertContains( 'C', $groups, 'User should be in group C' );
	}
}
