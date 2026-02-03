<?php

namespace BlueSpice\Tests\Api;

use BlueSpice\Tests\BSApiExtJSStoreTestBase;

/**
 * @group medium
 * @group api
 * @group Database
 * @group BlueSpice
 * @group BlueSpiceFoundation
 * @covers \BSApiCategoryTreeStore
 */
class BSApiCategoryTreeStoreTest extends BSApiExtJSStoreTestBase {

	protected $iFixtureTotal = 1;

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

	protected function setUp(): void {
		parent::setUp();
		$dbw = $this->getDb( DB_PRIMARY );

		// Parent category with no page
		$dbw->insert(
			'category',
			[
				'cat_id' => 1,
				'cat_title' => 'CategoryParent',
				'cat_pages' => 1,
				'cat_subcats' => 1,
				'cat_files' => 0
			],
			__METHOD__
		);

		// Subcategory with page
		$subCategoryPage = $this->insertPage(
			"CategorySub",
			"Text CategorySub",
			NS_CATEGORY
		);
		$subCategoryPageId = $subCategoryPage['id'];

		$lt_id = 1;
		$dbw->insert(
			'linktarget',
			[
				'lt_id' => $lt_id,
				'lt_namespace' => NS_CATEGORY,
				'lt_title' => 'CategoryParent'
			],
			__METHOD__
		);

		// Link subcategory to parent category
		$dbw->insert(
			'categorylinks',
			[
				'cl_from' => $subCategoryPageId,
				'cl_target_id' => $lt_id,
				'cl_timestamp' => $dbw->timestamp(),
				'cl_type' => 'subcat'
			],
			__METHOD__
		);
	}

	protected function createStoreFixtureData() {
		return 1;
	}

	protected function getModuleName() {
		return 'bs-category-treestore';
	}

	public function provideSingleFilterData() {
		return [
			'Filter by id' => [
				'string',
				'ct',
				'id',
				'src/CategoryParent',
				1
			]
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
						'value' => 'CategorySub'
					]
				],
				1
			]
		];
	}

	protected function getAdditionalParams() {
		return [
			'node' => 'src/CategoryParent'
		];
	}
}
