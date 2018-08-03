<?php

namespace BlueSpice\Tests\Api;

use BlueSpice\Tests\BSApiExtJSStoreTestBase;
use BlueSpice\Tests\BSPageFixturesProvider;

/**
 * @group Broken
 * @group medium
 * @group api
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class BSApiWikiPageStoreTest extends BSApiExtJSStoreTestBase {

	protected function skipAssertTotal() {
		return true;
	}

	protected function getStoreSchema() {
		return [
			'page_id' => [
				'type' => 'integer'
			],
			'page_namespace' => [
				'type' => 'integer'
			],
			'page_title' => [
				'type' => 'string'
			]
		];
	}

	protected function createStoreFixtureData() {
		$oPageFixtures = new BSPageFixturesProvider();
		$aFixtures = $oPageFixtures->getFixtureData();
		foreach( $aFixtures as $aFixture ) {
			$this->insertPage( $aFixture[0], $aFixture[1] );
		}

		return 0;
	}

	protected function getModuleName() {
		return 'bs-wikipage-store';
	}

	public function provideSingleFilterData() {
		return [
			'Filter by page_title' => [ 'string', 'ct', 'page_title', 'Hello', 1 ]
		];
	}

	public function provideMultipleFilterData() {
		return [
			'Filter by page_namespace and page_title' => [
				[
					[
						'type' => 'numeric',
						'comparison' => 'eq',
						'field' => 'page_namespace',
						'value' => 12
					],
					[
						'type' => 'string',
						'comparison' => 'eq',
						'field' => 'page_title',
						'value' => 'Test'
					]
				],
				1
			]
		];
	}

	public function provideKeyItemData() {
		return array(
			[ 'page_title', 'Зарегистрируйтесь_сейчас' ],
			[ 'page_title', 'テスト' ],
			[ 'page_namespace', 10 ]
		);
	}
}
