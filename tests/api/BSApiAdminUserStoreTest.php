<?php

/**
 * @group medium
 * @group api
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class BSApiAdminUserStoreTest extends BSApiExtJSStoreTestBase {
	protected $iFixtureTotal = 3;

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
		return 3;
	}

	protected function getModuleName() {
		return 'bs-adminuser-store';
	}

	public function provideSingleFilterData() {
		return [
			'Filter by user_name' => [ 'string', 'ct', 'user_name', 'Sysop', 2 ]
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
						'value' => 'example.com'
					],
					[
						'type' => 'string',
						'comparison' => 'ct',
						'field' => 'user_name',
						'value' => 'Sysop'
					]
				],
				1
			]
		];
	}
}

