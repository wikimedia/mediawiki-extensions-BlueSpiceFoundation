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
class BSApiAdminUserStoreTest extends BSApiExtJSStoreTestBase {

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
			],
			'user_email' => [
				'type' => 'string'
			]
		];
	}

	protected function createStoreFixtureData() {
		self::$userFixtures = new BSUserFixtures( $this );
	}

	protected function getModuleName() {
		return 'bs-adminuser-store';
	}

	public function provideSingleFilterData() {
		return [
			'Filter by user_name' => [ 'string', 'ct', 'user_name', 'John', 1 ]
		];
	}

	public function provideMultipleFilterData() {
		return [
			'Filter by user_email and user_name' => [
				[
					[
						'type' => 'string',
						'comparison' => 'ct',
						'field' => 'user_email',
						'value' => 'example.doesnotexist'
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
			'Test user John: email' => [ "user_email", "j@example.doesnotexist" ],
			'Test user Paul: email' => [ "user_email", "p@example.doesnotexist" ]
		];
	}

	protected function skipAssertTotal() {
		return true;
	}
}
