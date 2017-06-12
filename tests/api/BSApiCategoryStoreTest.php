<?php

/**
 * The base fixture has changed since MW 1.27. Therefore the test is marked
 * broken until BlueSpice updates its compatibility.
 * @group Broken
 * @group medium
 * @group api
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class BSApiCategoryStoreTest extends BSApiExtJSStoreTestBase {
	protected $iFixtureTotal = 2;
	protected $tablesUsed = [ 'category', 'categorylinks', 'page' ];

	protected function getStoreSchema() {
		return [
			'cat_id' => [
				'type' => 'integer'
			],
			'cat_title' => [
				'type' => 'string'
			],
			'text' => [
				'type' => 'string'
			],
			'cat_pages' => [
				'type' => 'integer'
			],
			'cat_subcats' => [
				'type' => 'integer'
			],
			'cat_files' => [
				'type' => 'integer'
			],
			'prefixed_text' => [
				'type' => 'sting'
			]
		];
	}

	protected function createStoreFixtureData() {
		$oDbw = wfGetDB( DB_MASTER );
		$oDbw->insert( 'category', array(
			'cat_title' => "Dummy test",
			'cat_pages' => 3,
			'cat_files' => 1
		) );

		$oDbw->insert( 'category', array(
			'cat_title' => "Dummy test 2",
			'cat_pages' => 2,
			'cat_files' => 3
		) );

		$oDbw->insert( 'categorylinks', array(
			'cl_to' => "Dummy test"
		) );

		$oDbw->insert( 'page', array(
			'page_title' => "Dummy test 2",
			'page_namespace' => NS_CATEGORY
		) );

		return 2;
	}

	protected function getModuleName() {
		return 'bs-category-store';
	}

	public function provideSingleFilterData() {
		return [
			'Filter by cat_title' => [ 'string', 'ct', 'cat_title', 'test', 2 ]
		];
	}

	public function provideMultipleFilterData() {
		return [
			'Filter by cat_pages and cat_title' => [
				[
					[
						'type' => 'numeric',
						'comparison' => 'eq',
						'field' => 'cat_pages',
						'value' => 3
					],
					[
						'type' => 'string',
						'comparison' => 'ct',
						'field' => 'cat_title',
						'value' => 'test'
					]
				],
				1
			]
		];
	}
}

