<?php

namespace BlueSpice\Tests\DataSources\Filter;

use BlueSpice\Data\Filter;
use BlueSpice\Data\Record;

/**
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class NumericTest extends \PHPUnit\Framework\TestCase {
	public function testPositive() {
		$filter = new Filter\Numeric( [
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
		$filter = new Filter\Numeric( [
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
