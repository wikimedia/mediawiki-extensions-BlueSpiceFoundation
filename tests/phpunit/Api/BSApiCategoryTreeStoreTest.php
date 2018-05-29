<?php

namespace BlueSpice\Tests\Api;

use BlueSpice\Tests\BSApiExtJSStoreTestBase;
/**
 * The base fixture has changed since MW 1.27. Therefore the test is marked
 * broken until BlueSpice updates its compatibility.
 * @group medium
 * @group api
 * @group Database
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class BSApiCategoryTreeStoreTest extends BSApiExtJSStoreTestBase {
	protected $iFixtureTotal = 2;
	protected $tablesUsed = [ 'category', 'categorylinks', 'page' ];

	protected function getStoreSchema() {
		return [
			'text' => [
				'type' => 'string'
			],
			'leaf' => [
				'type' => 'boolean'
			],
			'id' => [
				'type' => 'string'
			]
		];
	}

	protected function setUp() {
		parent::setUp();
		$oDbw = $this->db;
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

		$this->insertPage( "Dummy test 2", "Text Dummy test 2", NS_CATEGORY );
	}

	protected function createStoreFixtureData() {
		return 2;
	}

	protected function getModuleName() {
		return 'bs-category-treestore';
	}

	public function provideSingleFilterData() {
		return [
			'Filter by id' => [ 'string', 'ct', 'id', 'src/', 2 ]
		];
	}

	public function provideMultipleFilterData() {
		return [
			'Filter by leaf and text' => [
				[
					[
						'type' => 'boolean',
						'comparison' => 'eq',
						'field' => 'leaf',
						'value' => true
					],
					[
						'type' => 'string',
						'comparison' => 'eq',
						'field' => 'text',
						'value' => 'Dummy test 2'
					]
				],
				1
			]
		];
	}

	protected function getAdditionalParams() {
		return [
			'node' => 'src'
		];
	}
}
