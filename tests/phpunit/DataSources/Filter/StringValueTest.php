<?php

namespace BlueSpice\Tests\DataSources\Filter;

use BlueSpice\Data\Filter;
use BlueSpice\Data\Record;

/**
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class StringValueTest extends \PHPUnit\Framework\TestCase {
	public function testPositive() {
		$filter = new Filter\StringValue( [
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
		$filter = new Filter\StringValue( [
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
