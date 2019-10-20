<?php

namespace BlueSpice\Tests\Html\Descriptor;

use Exception;
use PHPUnit\Framework\TestCase;
use BlueSpice\Html\Descriptor\SimpleLink;

class SimpleLinkTest extends TestCase {

	public function testConstructorExceptionLabel() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( "Field 'label' must be provided!" );
		$link = new SimpleLink( [] );
	}

	public function testConstructorExceptionTooltip() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( "Field 'tooltip' must be provided!" );
		$link = new SimpleLink( [
			'label' => 'Some label'
		] );
	}

	public function testConstructorExceptionHref() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( "Field 'href' must be provided!" );
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
