<?php

namespace BlueSpice\Tests\DataSources\Filter;

use BlueSpice\Data\Filter;
use BlueSpice\Data\Record;

/**
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class BooleanTest extends \PHPUnit\Framework\TestCase {
	public function testPositive() {
		$filter = new Filter\Boolean( [
			'field' => 'field1',
			'comparison' => 'eq',
			'value' => true
		] );

		$result = $filter->matches( new Record( (object)[
			'field1' => true,
			'field2' => false
		] ) );

		$this->assertTrue( $result );
	}

	public function testNegative() {
		$filter = new Filter\Boolean( [
			'field' => 'field1',
			'comparison' => 'eq',
			'value' => false
		] );

		$result = $filter->matches( new Record( (object)[
			'field1' => true,
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
		$filter = new Filter\Boolean( [
			Filter\Boolean::KEY_FIELD => 'field_A',
			Filter\Boolean::KEY_VALUE => $filterValue,
			Filter\Boolean::KEY_COMPARISON => $comparison
		] );

		$dataSet = new Record( (object) [
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
			[ true, Filter\Boolean::COMPARISON_EQUALS, true, true ],
			[ true, Filter\Boolean::COMPARISON_EQUALS, 1, true ],
			[ true, Filter\Boolean::COMPARISON_EQUALS, '1', true ],
			[ true, Filter\Boolean::COMPARISON_EQUALS, false, false ],
			[ true, Filter\Boolean::COMPARISON_EQUALS, 0, false ],
			[ true, Filter\Boolean::COMPARISON_EQUALS, '0', false ],
			[ true, Filter\Boolean::COMPARISON_NOT_EQUALS, true, false ],
			[ true, Filter\Boolean::COMPARISON_NOT_EQUALS, 1, false ],
			[ true, Filter\Boolean::COMPARISON_NOT_EQUALS, '1', false ],
			[ true, Filter\Boolean::COMPARISON_NOT_EQUALS, false, true ],
			[ true, Filter\Boolean::COMPARISON_NOT_EQUALS, 0, true ],
			[ true, Filter\Boolean::COMPARISON_NOT_EQUALS, '0', true ],
			[ false, Filter\Boolean::COMPARISON_EQUALS, true, false ],
			[ false, Filter\Boolean::COMPARISON_EQUALS, 1, false ],
			[ false, Filter\Boolean::COMPARISON_EQUALS, '1', false ],
			[ false, Filter\Boolean::COMPARISON_EQUALS, false, true ],
			[ false, Filter\Boolean::COMPARISON_EQUALS, 0, true ],
			[ false, Filter\Boolean::COMPARISON_EQUALS, '0', true ],
			[ false, Filter\Boolean::COMPARISON_NOT_EQUALS, true, true ],
			[ false, Filter\Boolean::COMPARISON_NOT_EQUALS, 1, true ],
			[ false, Filter\Boolean::COMPARISON_NOT_EQUALS, '1', true ],
			[ false, Filter\Boolean::COMPARISON_NOT_EQUALS, false, false ],
			[ false, Filter\Boolean::COMPARISON_NOT_EQUALS, 0, false ],
			[ false, Filter\Boolean::COMPARISON_NOT_EQUALS, '0', false ]
		];
	}
}
