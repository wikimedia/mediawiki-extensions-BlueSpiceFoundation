<?php

namespace BlueSpice\Tests\Data\Filter;

use BlueSpice\Data\Record;

/**
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class StringValueTest extends \PHPUnit_Framework_TestCase {
	public function testPositive() {
		$filter = new \BlueSpice\Data\Filter\StringValue( [
			'field' => 'field1',
			'comparison' => 'ct',
			'value' => 'ello'
		] );

		$result = $filter->matches( new Record( (object) [
			'field1' => 'Hello World',
			'field2' => 'Hallo Welt'
		] ) );

		$this->assertTrue( $result );
	}

	public function testNegative() {
		$filter = new \BlueSpice\Data\Filter\StringValue( [
			'field' => 'field1',
			'comparison' => 'ct',
			'value' => 'allo'
		] );

		$result = $filter->matches( new Record( (object)[
			'field1' => 'Hello World',
			'field2' => 'Hallo Welt'
		] ) );

		$this->assertFalse( $result );
	}
}
