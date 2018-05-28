<?php

namespace BlueSpice\Tests\Api;

use BlueSpice\Tests\BSApiExtJSStoreTestBase;

/**
 * Test behavior has changed on MW > 1.27. Therefore this test is currently
 * broken until BlueSpice updates compatibility
 * @group Broken
 * @group medium
 * @group api
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class BSApiGroupStoreTest extends BSApiExtJSStoreTestBase {

	protected function getStoreSchema() {
		return [
			'group_name' => [
				'type' => 'string'
			],
			'additional_group' => [
				'type' => 'boolean'
			],
			'displayname' => [
				'type' => 'string'
			]
		];
	}

	protected function createStoreFixtureData() {
		global $wgGroupPermissions, $wgAdditionalGroups;

		$wgGroupPermissions['dummy']['read'] = true;
		$wgGroupPermissions['fake']['read'] = true;
		$wgAdditionalGroups['dummy'] = [];
		$wgAdditionalGroups['fake'] = [];

		return 0;
	}

	protected function getModuleName() {
		return 'bs-group-store';
	}

	public function provideSingleFilterData() {
		return [
			'Filter by group_name' => [ 'string', 'eq', 'group_name', 'sysop', 1 ],
			'Filter by additional_group' => [ 'boolean', 'eq', 'additional_group', true, 2 ]
		];
	}

	public function provideMultipleFilterData() {
		return [
			'Filter by group_name and additional_group' => [
				[
					[
						'type' => 'string',
						'comparison' => 'eq',
						'field' => 'group_name',
						'value' => 'fake'
					],
					[
						'type' => 'boolean',
						'comparison' => 'eq',
						'field' => 'additional_group',
						'value' => true
					]
				],
				1
			]
		];
	}

	protected function skipAssertTotal() {
		return true;
	}

	public function provideKeyItemData() {
		return array(
			[ 'group_name', 'sysop' ],
			[ 'group_name', 'bot' ],
			[ 'group_name', 'dummy' ]
		);
	}
}
