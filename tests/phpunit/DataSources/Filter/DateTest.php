<?php

namespace BlueSpice\Tests\DataSources\Filter;

use BlueSpice\Data\Filter;
use BlueSpice\Data\Record;

/**
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class DateTest extends \PHPUnit\Framework\TestCase {
	public function testPositive() {
		$filter = new Filter\Date( [
			'field' => 'field1',
			'comparison' => 'gt',
			'value' => '2017/01/01'
		] );

		$result = $filter->matches( new Record( (object)[
			'field1' => '20170101000001',
			'field2' => false
		] ) );

		$this->assertTrue( $result );
	}

	public function testNegative() {
		$filter = new Filter\Date( [
			'field' => 'field1',
			'comparison' => 'gt',
			'value' => '2017/01/02'
		] );

		$result = $filter->matches( new Record( (object)[
			'field1' => '20170101000001',
			'field2' => false
		] ) );

		$this->assertFalse( $result );
	}

	/**
	 * @dataProvider provideAppliesToValues
	 * @param boolean $expecation
	 * @param mixed $fieldValue
	 * @param mixed $filterValue
	 * @param string $comparison
	 */
	public function testAppliesTo ( $expectation, $comparison, $fieldValue, $filterValue ) {
		$filter = new Filter\Date([
			Filter::KEY_FIELD => 'field_A',
			Filter::KEY_VALUE => $filterValue,
			Filter::KEY_COMPARISON => $comparison
		]);

		$dataSet = new Record( (object)[
			'field_A' => $fieldValue
		] );

		if( $expectation ) {
			$this->assertTrue( $filter->matches( $dataSet ), 'Filter should apply' );
		}
		else {
			$this->assertFalse( $filter->matches( $dataSet ), 'Filter should not apply' );
		}
	}

	public function provideAppliesToValues() {
		return [
			[ true, Filter::COMPARISON_EQUALS, '2017-07-01', '20170701000000' ],
			[ true, Filter::COMPARISON_EQUALS, 0, '1970-01-01' ],
			[ true, Filter::COMPARISON_EQUALS, '1970/01/02', '1970-01-02' ],
			[ true, Filter\Range::COMPARISON_GREATER_THAN, '1970/01/02', 1 ],
			[ true, Filter\Range::COMPARISON_LOWER_THAN, '1970/01/02', 'now' ],
			[ true, Filter\Range::COMPARISON_LOWER_THAN, 'now - 1 week', 'now' ],
			[ false, Filter\Range::COMPARISON_EQUALS, 'now - 1 week', 'now' ],
		];
	}
}
