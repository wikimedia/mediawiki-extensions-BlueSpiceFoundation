<?php

namespace BlueSpice\Tests\DataSources;

use BlueSpice\Data\ReaderParams;

/**
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class ReaderParamsTest extends \PHPUnit\Framework\TestCase {
	/**
	 * @covers BlueSpice\Data\ReaderParams::__construct
	 */
	public function testInitFromArray() {
		$params = new ReaderParams( [
			'query' => 'Some query',
			'limit' => 50,
			'start' => 100,
			'sort' => [
				[ 'property' => 'prop_a', 'direction' => 'asc' ],
				[ 'property' => 'prop_b', 'direction' => 'desc' ]
			],
			'filter' => [
				[ 'type' => 'string',
					'comparison' => 'ct',
					'value' => 'test',
					'field' => 'prop_a'
				],
				[
					'type' => 'numeric',
					'comparison' => 'gt',
					'value' => 99,
					'field' => 'prop_b'
				]
			]
		] );

		$this->assertInstanceOf( '\BlueSpice\Data\ReaderParams', $params );

		// TODO: Split test
		$this->assertEquals( 'Some query', $params->getQuery() );
		$this->assertEquals( 100, $params->getStart() );
		$this->assertEquals( 50, $params->getLimit() );

		$sort = $params->getSort();
		$this->assertCount( 2, $sort );
		$firstSort = $sort[0];
		$this->assertInstanceOf(
			'\BlueSpice\Data\Sort',  $firstSort
		);

		$this->assertEquals(
			\BlueSpice\Data\Sort::ASCENDING,
			$firstSort->getDirection()
		);

		$filter = $params->getFilter();
		$this->assertCount( 2, $filter );

		$firstFilter = $filter[0];
		$this->assertInstanceOf(
			'\BlueSpice\Data\Filter',  $firstFilter
		);

		$this->assertEquals(
			\BlueSpice\Data\Filter\StringValue::COMPARISON_CONTAINS,
			$firstFilter->getComparison()
		);

		$filedNames = [];
		foreach ( $filter as $filterObject ) {
			$filedNames[] = $filterObject->getField();
		}

		$this->assertContains( 'prop_a', $filedNames );
	}
}
