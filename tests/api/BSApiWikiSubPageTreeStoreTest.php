<?php

/**
 * @group medium
 * @group api
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class BSApiWikiSubPageTreeStoreTest extends BSApiExtJSStoreTestBase {
	protected $iFixtureTotal = 2;

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

	protected function createStoreFixtureData() {

		$aFixtures = [];
		$aFixtures[] = [ "Help:Dummy", "Dummy text" ];
		$aFixtures[] = [ "Help:Dummy/First", "Dummy text" ];
		$aFixtures[] = [ "Help:Dummy/Second", "Dummy text" ];
		foreach( $aFixtures as $aFixture ) {
			$this->insertPage( $aFixture[0], $aFixture[1] );
		}

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
		return array(
			[ 'text', 'First' ],
			[ 'text', 'Second' ],
			[ 'id', 'Help:Dummy/First' ]
		);
	}

	protected function getResultsNodeName() {
		return 'children';
	}
}

