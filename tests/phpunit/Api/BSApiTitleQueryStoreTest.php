<?php

namespace BlueSpice\Tests\Api;

use BlueSpice\Tests\BSApiExtJSStoreTestBase;
use BlueSpice\Tests\BSPageFixturesProvider;

/**
 * @group medium
 * @group API
 * @group Database
 * @group BlueSpice
 * @group BlueSpiceFoundation
 *
 * Class BSApiTitleQueryStoreTest
 */
class BSApiTitleQueryStoreTest extends BSApiExtJSStoreTestBase {
	protected function getStoreSchema() {
		return [
			'page_id' => [
				'type' => 'numeric'
			],
			'page_namespace' => [
				'type' => 'numeric'
			],
			'page_title' => [
				'type' => 'string'
			],
			'prefixedText' => [
				'type' => 'string'
			],
			'displayText' => [
				'type' => 'string'
			],
			'type' => [
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
		$total = count( $aFixtures );
		return $total;
	}

	protected function getModuleName() {
		return 'bs-titlequery-store';
	}

	protected function makeRequestParams() {
		$aParams =  parent::makeRequestParams();
		$aParams['options'] = \FormatJson::encode([
			'namespaces' => [ NS_MAIN, NS_TEMPLATE ]
		]);

		return $aParams;
	}

	public function provideSingleFilterData() {
		return [
			'Filter by page_id' => [ 'numeric', 'eq', 'page_id', -1, 0 ]
		];
	}

	public function provideMultipleFilterData() {
		return [
			'Filter by page_name and page_namespace' => [
				[

				],
				0
			]
		];
	}

	/**
	 * @group Broken
	 * @param int $limit
	 * @param int $offset
	 */
	public function testPaging($limit, $offset) {
		parent::testPaging($limit, $offset);
	}

	/**
	 * @group Broken
	 * @param array $filters
	 * @param integer $expectedTotal
	 */
	public function testMultipleFilter( $filters, $expectedTotal ) {
		parent::testMultipleFilter( $filters, $expectedTotal );
	}
}
