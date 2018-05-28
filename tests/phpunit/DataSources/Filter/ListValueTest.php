<?php

namespace BlueSpice\Tests\DataSources\Filter;

use BlueSpice\Data\Filter;
use BlueSpice\Data\Record;

/**
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class ListValueTest extends \PHPUnit\Framework\TestCase {
	public function testPositive() {
		$filter = new Filter\ListValue( [
			'field' => 'field1',
			'comparison' => 'ct',
			'value' => [ 'Hello' ]
		] );

		$result = $filter->matches( new Record( (object)[
			'field1' => [ 'Hello', 'World' ],
			'field2' => false
		] ) );

		$this->assertTrue( $result );
	}

	public function testNegative() {
		$filter = new Filter\ListValue( [
			'field' => 'field1',
			'comparison' => 'ct',
			'value' => [ 'Hello' ]
		] );

		$result = $filter->matches( new Record( (object)[
			'field1' => [ 'Hallo', 'Welt' ],
			'field2' => false
		] ) );

		$this->assertFalse( $result );
	}
}
