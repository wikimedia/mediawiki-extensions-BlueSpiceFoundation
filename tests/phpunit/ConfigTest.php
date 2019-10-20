<?php

namespace BlueSpice\Tests;

use MediaWiki\MediaWikiServices;

/**
 * @group Database
 * @group BlueSpice
 * @group BlueSpiceFoundation
 * @group Medium
 */
class ConfigTest extends \MediaWikiTestCase {
	protected $tablesUsed = [ 'bs_settings3' ];

	public function setUp() : void {
		parent::setUp();

		$GLOBALS['bsgUnitTestSetting']  = 5;
		$GLOBALS['bsgUnitTestSetting2'] = [ 'An', 'array' ];
	}

	public function addDBData() {
		parent::addDBData();
		$this->db->insert( 'bs_settings3', [
			's_name' => 'UnitTestSetting',
			// JSON formatted
			's_value' => '"9"'
		] );
		$config = new \BlueSpice\Config(
			\MediaWiki\MediaWikiServices::getInstance()->getDBLoadBalancer()
		);
		$config->invalidateCache();
	}

	public function testFactoryReturn() {
		$config = MediaWikiServices::getInstance()->getConfigFactory()
			->makeConfig( 'bsg' );

		$this->assertInstanceOf(
			// Can be discussed whether just \Config is sufficient to test
			'\\BlueSpice\\Config',
			$config,
			'MediaWiki ConfigFactory should return propert instance'
		);
	}

	public function testDatabasePreval() {
		$config = new \BlueSpice\Config(
			\MediaWiki\MediaWikiServices::getInstance()->getDBLoadBalancer()
		);
		$this->assertEquals(
			9,
			$config->get( 'UnitTestSetting' ),
			'Should return value from database'
		);
	}

	public function testGlobalVarDefaulting() {
		$config = new \BlueSpice\Config(
			\MediaWiki\MediaWikiServices::getInstance()->getDBLoadBalancer()
		);

		$this->assertArrayEquals(
			[ 'An', 'array' ],
			$config->get( 'UnitTestSetting2' ),
			'Should fall back to global vars'
		);
	}
}
