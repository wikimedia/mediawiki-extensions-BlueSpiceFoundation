<?php

namespace BlueSpice\Tests\Api;

use BlueSpice\Tests\BSApiExtJSStoreTestBase;

/**
 * @group medium
 * @group api
 * @group Database
 * @group BlueSpice
 * @group BlueSpiceFoundation
 * @covers \BSApiWikiSubPageTreeStore
 */
class BSApiWikiSubPageTreeStoreTest extends BSApiExtJSStoreTestBase {
	protected $iFixtureTotal = 2;
	protected $tablesUsed = [ 'page' ];

	protected function getStoreSchema() {
		return [
			'text' => [
				'type' => 'string'
			],
			'id' => [
				'type' => 'string'
			],
			'page_link' => [
				'type' => 'string'
			],
			'leaf' => [
				'type' => 'boolean'
			],
			'expanded' => [
				'type' => 'boolean'
			],
			'loaded' => [
				'type' => 'boolean'
			]
		];
	}

	protected function setUp(): void {
		parent::setUp();
		$this->insertPage( 'Help:Dummy' );
		$this->insertPage( 'Help:Dummy/First' );
		$this->insertPage( 'Help:Dummy/Second' );
	}

	protected function createStoreFixtureData() {
		return 2;
	}

	protected function getModuleName() {
		return 'bs-wikisubpage-treestore';
	}

	public function provideSingleFilterData() {
		return [
			'Filter by text' => [ 'string', 'eq', 'text', 'First', 1 ]
		];
	}

	public function provideMultipleFilterData() {
		return [
			'Filter by text and page_link' => [
				[
					[
						'type' => 'string',
						'comparison' => 'eq',
						'field' => 'text',
						'value' => 'Second'
					],
					[
						'type' => 'string',
						'comparison' => 'ct',
						'field' => 'page_link',
						'value' => 'Dummy'
					]
				],
				1
			]
		];
	}

	protected function getAdditionalParams() {
		return [
			'node' => 'Help:Dummy'
		];
	}

	public function provideKeyItemData() {
		return [
			[ 'text', 'First' ],
			[ 'text', 'Second' ],
			[ 'id', 'Help:Dummy/First' ]
		];
	}

	protected function getResultsNodeName() {
		return 'children';
	}
}
