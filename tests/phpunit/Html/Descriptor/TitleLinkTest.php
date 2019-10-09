<?php

namespace BlueSpice\Tests\Html\Descriptor;

use PHPUnit\Framework\TestCase;
use BlueSpice\Html\Descriptor\TitleLink;
use \BlueSpice\Html\Descriptor\ILink;

class TitleLinkTest extends TestCase {

	/**
	 * @convers TitleLink::__construct
	 */
	public function testConstructor() {
		$context = $this->createMock( '\IContextSource' );
		$config = $this->createMock( '\Config' );

		$link = new TitleLink( $context, $config );

		$this->assertInstanceOf( ILink::class, $link );
	}

	/**
	 * @convers TitleLink::getCSSClasses
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
	 * @convers TitleLink::getCSSClasses
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
