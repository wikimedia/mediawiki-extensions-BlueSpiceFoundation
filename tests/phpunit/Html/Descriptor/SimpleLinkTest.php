<?php

namespace BlueSpice\Tests\Html\Descriptor;

use PHPUnit\Framework\TestCase;
use BlueSpice\Html\Descriptor\SimpleLink;

class SimpleLinkTest extends TestCase {

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Field 'label' must be provided!
	 */
	public function testConstructorExceptionLabel() {
		$link = new SimpleLink( [] );
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Field 'tooltip' must be provided!
	 */
	public function testConstructorExceptionTooltip() {
		$link = new SimpleLink( [
			'label' => 'Some label'
		] );
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Field 'href' must be provided!
	 */
	public function testConstructorExceptionHref() {
		$link = new SimpleLink( [
			'label' => 'Some label',
			'tooltip' => 'Some tooltip'
		] );
	}

	public function testAllGetters() {
		$link = new SimpleLink( [
			'label' => 'Some label',
			'tooltip' => 'Some tooltip',
			'href' => 'https://bluespice.com',
			'data-attributes' => [
				'some' => 'value'
			]
		] );

		$this->assertEmpty( $link->getHtmlId() );
		$this->assertEmpty( $link->getCSSClasses() );
		$this->assertEmpty( $link->getIcon() );
		$this->assertEquals( 'Some label', $link->getLabel() );
		$this->assertEquals( 'Some tooltip', $link->getTooltip() );
		$this->assertEquals( 'https://bluespice.com', $link->getHref() );
		$this->assertArrayHasKey( 'some', $link->getDataAttributes() );
	}

}
