<?php

namespace BlueSpice\Tests\Api;

use BlueSpice\Tests\BSApiExtJSStoreTestBase;

/**
 * There was a change in adressing IW links in MW > 1.27. BlueSpice is testing
 * with MW 1.27, so adding the group "Broken" until we update our compatibility
 * @group medium
 * @group api
 * @group Database
 * @group BlueSpice
 * @group BlueSpiceFoundation
 * @group Daniel
 */
class BSApiInterwikiStoreTest extends BSApiExtJSStoreTestBase {
	protected $iFixtureTotal = 3;

	protected function getStoreSchema() {
		return [
			'iw_prefix' => [
				'type' => 'string'
			],
			'iw_url' => [
				'type' => 'string'
			],
			'iw_api' => [
				'type' => 'string'
			],
			'iw_wikiid' => [
				'type' => 'string'
			],
			'iw_local' => [
				'type' => 'string'
			],
			'iw_trans' => [
				'type' => 'string'
			]
		];
	}

	protected function createStoreFixtureData() {
		$oDbw = $this->db;
		$oDbw->insert( 'interwiki', array(
			'iw_prefix' => "Dummy",
			'iw_url' => "http://wiki.dummy.org/$1",
			'iw_api' => '',
			'iw_wikiid' => '',
			'iw_local' => "1"
		) );

		$oDbw->insert( 'interwiki', array(
			'iw_prefix' => "Demo",
			'iw_url' => "http://wiki.demo.org/$1",
			'iw_api' => "http://wiki.demo.org/api.php",
			'iw_wikiid' => '',
			'iw_local' => '0'
		) );

		$oDbw->insert( 'interwiki', array(
			'iw_prefix' => "Sample",
			'iw_url' => "http://wiki.sample.org/$1",
			'iw_api' => '',
			'iw_wikiid' => '',
			'iw_trans' => "1",
			'iw_local' => "1"
		) );
		return 3;
	}

	protected function getModuleName() {
		return 'bs-interwiki-store';
	}

	public function provideSingleFilterData() {
		return [
			'Filter by iw_url' => [ 'string', 'ct', 'iw_url', 'wiki', 3 ],
			'Filter by iw_prefix' => [ 'string', 'eq', 'iw_prefix', 'Sample', 1 ]
		];
	}

	public function provideMultipleFilterData() {
		return [
			'Filter by iw_local and iw_prefix' => [
				[
					[
						'type' => 'string',
						'comparison' => 'eq',
						'field' => 'iw_local',
						'value' => '1'
					],
					[
						'type' => 'string',
						'comparison' => 'eq',
						'field' => 'iw_prefix',
						'value' => 'Dummy'
					]
				],
				1
			]
		];
	}

	public function provideKeyItemData() {
		return array(
			[ 'iw_url', 'http://wiki.demo.org/$1' ],
			[ 'iw_api', 'http://wiki.demo.org/api.php' ],
			[ 'iw_prefix', 'Demo' ]
		);
	}
}
