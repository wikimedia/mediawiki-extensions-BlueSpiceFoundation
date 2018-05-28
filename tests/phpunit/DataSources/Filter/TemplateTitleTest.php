<?php

namespace BlueSpice\Tests\DataSources\Filter;

use BlueSpice\Data\Filter;
use BlueSpice\Data\Record;

/**
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class TemplateTitleTest extends \PHPUnit\Framework\TestCase {
	public function testPositive() {
		$filter = new Filter\TemplateTitle( [
			'field' => 'field1',
			'comparison' => 'eq',
			'value' => 'Help'
		] );

		$result = $filter->matches( new Record( (object)[
			'field1' => 'Template:Help',
			'field2' => 'User:WikiSysop'
		] ) );

		$this->assertTrue( $result );
	}

	public function testNegative() {
		$filter = new Filter\TemplateTitle( [
			'field' => 'field1',
			'comparison' => 'eq',
			'value' => 'Help'
		] );

		$result = $filter->matches( new Record( (object)[
			'field1' => 'Vorlage:Hilfe',
			'field2' => 'User:WikiSysop'
		] ) );

		$this->assertFalse( $result );
	}
}
