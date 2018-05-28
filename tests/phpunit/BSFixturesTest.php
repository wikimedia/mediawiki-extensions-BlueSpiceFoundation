<?php

namespace BlueSpice\Tests;

use BlueSpice\Tests\BSApiTestCase;
use BlueSpice\Tests\BSPageFixtures;
use BlueSpice\Tests\BSUserFixtures;

/**
 * @group medium
 * @group Database
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class BSFixturesTest extends BSApiTestCase {

	public function setUp() {
		parent::setUp();
		new BSPageFixtures( $this );
		new BSUserFixtures( $this );
	}

	public function testPageFixtures() {
		$title = \Title::newFromText( 'Template:Hello World' );
		$this->assertTrue( $title->exists(), 'Title should be known' );
	}

	public function testUserFixtures() {
		$user = \User::newFromName( 'Paul' );
		$this->assertFalse( $user->isAnon(), "User should be known" );


		$groups = $user->getGroups();

		$this->assertTrue( in_array( 'A', $groups ), 'User should be in group A' );
		$this->assertFalse( in_array( 'B', $groups ), 'User should not be in group B' );
		$this->assertTrue( in_array( 'C', $groups ), 'User should be in group C' );
	}
}
