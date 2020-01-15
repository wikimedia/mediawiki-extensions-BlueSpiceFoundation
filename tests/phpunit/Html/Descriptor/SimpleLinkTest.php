<?php

namespace BlueSpice\Tests\Html\Descriptor;

use BlueSpice\Html\Descriptor\SimpleLink;
use Exception;
use PHPUnit\Framework\TestCase;

class SimpleLinkTest extends TestCase {

	/**
	 * @covers BlueSpice\Html\Descriptor\SimpleLink::__construct
	 */
	public function testConstructorExceptionLabel() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( "Field 'label' must be provided!" );
		$link = new SimpleLink( [] );
	}

	/**
	 * @covers BlueSpice\Html\Descriptor\SimpleLink::__construct
	 */
	public function testConstructorExceptionTooltip() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( "Field 'tooltip' must be provided!" );
		$link = new SimpleLink( [
			'label' => 'Some label'
		] );
	}

	/**
	 * @covers BlueSpice\Html\Descriptor\SimpleLink::__construct
	 */
	public function testConstructorExceptionHref() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( "Field 'href' must be provided!" );
		$link = new SimpleLink( [
			'label' => 'Some label',
			'tooltip' => 'Some tooltip'
		] );
	}

	/**
	 * @covers BlueSpice\Html\Descriptor\SimpleLink::getFromData
	 */
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
