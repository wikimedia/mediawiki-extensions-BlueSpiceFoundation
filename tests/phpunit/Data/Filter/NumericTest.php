<?php

namespace BlueSpice\Tests\Data\Filter;

use BlueSpice\Data\Record;

/**
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class NumericTest extends \PHPUnit\Framework\TestCase {
	public function testPositive() {
		$filter = new \BlueSpice\Data\Filter\Numeric( [
			'field' => 'field1',
			'comparison' => 'gt',
			'value' => 5
		] );

		$result = $filter->matches( new Record( (object)[
			'field1' => 7,
			'field2' => 3
		] ) );

		$this->assertTrue( $result );
	}

	public function testNegative() {
		$filter = new \BlueSpice\Data\Filter\Numeric( [
			'field' => 'field1',
			'comparison' => 'gt',
			'value' => 5
		] );

		$result = $filter->matches( new Record( (object)[
			'field1' => 3,
			'field2' => 7
		] ) );

		$this->assertFalse( $result );
	}
}
