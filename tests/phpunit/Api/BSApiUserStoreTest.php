<?php

namespace BlueSpice\Tests\Api;

use BlueSpice\Tests\BSApiExtJSStoreTestBase;
use BlueSpice\Tests\BSUserFixtures;

/**
 * @group medium
 * @group api
 * @group Database
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class BSApiUserStoreTest extends BSApiExtJSStoreTestBase {
	protected function getStoreSchema() {
		return [
			'user_id' => [
				'type' => 'integer'
			],
			'user_name' => [
				'type' => 'string'
			],
			'user_real_name' => [
				'type' => 'string'
			],
			'user_registration' => [
				'type' => 'string'
			],
			'user_editcount' => [
				'type' => 'integer'
			],
			'groups' => [
				'type' => 'array'
			],
			'enabled' => [
				'type' => 'boolean'
			],
			'page_link' => [
				'type' => 'string'
			],
			'page_prefixed_text' => [
				'type' => 'string'
			]
		];
	}

	protected function createStoreFixtureData() {
		self::$userFixtures = new BSUserFixtures( $this );
	}

	protected function getModuleName() {
		return 'bs-user-store';
	}

	public function provideSingleFilterData() {
		return [
			'Filter by user_name' => [ 'string', 'ct', 'user_name', 'John', 1 ]
		];
	}

	public function provideMultipleFilterData() {
		return [
			'Filter by display_name and user_name' => [
				[
					[
						'type' => 'string',
						'comparison' => 'eq',
						'field' => 'display_name',
						'value' => 'John L.'
					],
					[
						'type' => 'string',
						'comparison' => 'ct',
						'field' => 'user_name',
						'value' => 'John'
					]
				],
				1
			]
		];
	}

	public function provideKeyItemData() {
		return[
			'Test user John: name' => [ "user_name", "John" ],
			'Test user Paul: name' => [ "user_name", "Paul" ],
		];
	}

	protected function skipAssertTotal() {
		return true;
	}

}
