<?php

namespace BlueSpice\Tests\DataSources;

use BlueSpice\Data\Sorter;
use BlueSpice\Data\Sort;
use BlueSpice\Data\Record;

/**
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class SorterTest extends \PHPUnit\Framework\TestCase {
	protected $testDataSets = [
		[
			'field1' => 1,
			'field2' => '20170101000000',
			'field3' => '1 item',
			'field4' => [ 1, 2, 3 ],
			'field5' => [
				[ 'a' => 1 ],
				[ 'a' => 2 ],
				[ 'a' => 3 ]
			]
		],
		[
			'field1' => 3,
			'field2' => '20170101000002',
			'field3' => '10 items',
			'field4' => [ 2, 3, 4 ],
			'field5' => [
				[ 'a' => 1 ],
				[ 'a' => 2 ],
				[ 'a' => 3 ]
			]
		],
		[
			'field1' => 4,
			'field2' => '20170101000001',
			'field3' => 'an item',
			'field4' => [ 4, 5, 6 ],
			'field5' => [
				[ 'a' => 1 ],
				[ 'a' => 2 ],
				[ 'a' => 3 ]
			]
		],
		[
			'field1' => 2,
			'field2' => '20170101000003',
			'field3' => 'An eloquent item',
			'field4' => [ 3, 1, 2 ],
			'field5' => [
				[ 'a' => 1 ],
				[ 'a' => 2 ],
				[ 'a' => 3 ]
			]
		],

		//Identically to filed1=4 but with different timestamp to allow testing
		//of multible sorters
		[
			'field1' => 5,
			'field2' => '20170101000002',
			'field3' => 'an item',
			'field4' => [ 4, 5, 6 ],
			'field5' => [
				[ 'a' => 1 ],
				[ 'a' => 2 ],
				[ 'a' => 3 ]
			]
		],
	];

	/**
	 *
	 * @param array $sort
	 * @param array $expectedSorting
	 * @dataProvider provideSortData
	 */
	public function testSort( $sort, $expectedSorting ) {
		$sorter = new Sorter( Sort::newCollectionFromArray( $sort ) );
		$dataSets = $this->makeDataSets();
		$sortedDataSets = $sorter->sort( $dataSets );

		foreach( $sortedDataSets as $index => $dataSet ) {
			$this->assertEquals( $expectedSorting[$index], $dataSet->get( 'field1' ) );
		}
	}

	public function provideSortData() {
		return [
			'numeric-asc' => [
				[ [ 'property' => 'field1', 'direction' => 'ASC' ] ],
				[ 1, 2, 3, 4, 5 ]
			],
			'numeric-desc' => [
				[ [ 'property' => 'field1', 'direction' => 'DESC' ] ],
				[ 5, 4, 3, 2, 1 ]
			],
			'string-asc' => [
				[ [ 'property' => 'field3', 'direction' => 'ASC' ] ],
				[ 1, 3, 2, 4, 5 ]
			],
			'datetime-asc' => [
				[ [ 'property' => 'field2', 'direction' => 'ASC' ] ],
				[ 1, 4, 3, 2, 5 ]
			],
			'datetime-asc' => [
				[ [ 'property' => 'field2', 'direction' => 'ASC' ] ],
				[ 1, 4, 3, 5, 2 ]
			]
		];
	}

	protected function makeDataSets() {
		$dataSets = [];
		foreach( $this->testDataSets as $dataSet ) {
			$dataSets[] = new Record( (object)$dataSet );
		}

		return $dataSets;
	}

}
