<?php

namespace BlueSpice\Tests\Html\Descriptor;

use BlueSpice\Html\Descriptor\ILink;
use BlueSpice\Html\Descriptor\TitleLink;
use PHPUnit\Framework\TestCase;

class TitleLinkTest extends TestCase {

	/**
	 * @covers TitleLink::__construct
	 */
	public function testConstructor() {
		$context = $this->createMock( '\IContextSource' );
		$config = $this->createMock( '\Config' );

		$link = new TitleLink( $context, $config );

		$this->assertInstanceOf( ILink::class, $link );
	}

	/**
	 * @covers TitleLink::getCSSClasses
	 */
	public function testExistingTitle() {
		$context = $this->createMock( '\IContextSource' );
		$config = $this->createMock( '\Config' );

		$title = $this->createMock( '\Title' );
		$title->method( 'exists' )->willReturn( true );
		$title->method( 'isExternal' )->willReturn( false );

		$link = new TitleLink( $context, $config, $title );

		$this->assertNotContains( 'new', $link->getCSSClasses() );
	}

	/**
	 * @covers TitleLink::getCSSClasses
	 */
	public function testNotExistingTitle() {
		$context = $this->createMock( '\IContextSource' );
		$config = $this->createMock( '\Config' );

		$title = $this->createMock( '\Title' );
		$title->method( 'exists' )->willReturn( false );
		$title->method( 'isExternal' )->willReturn( false );

		$link = new TitleLink( $context, $config, $title );

		$this->assertContains( 'new', $link->getCSSClasses() );
	}
}
