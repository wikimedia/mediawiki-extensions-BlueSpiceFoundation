<?php

namespace BlueSpice\Tests\Html\Descriptor;

use BlueSpice\Html\Descriptor\ILink;
use BlueSpice\Html\Descriptor\TitleLink;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Title\Title;
use PHPUnit\Framework\TestCase;

class TitleLinkTest extends TestCase {

	/**
	 * @covers \BlueSpice\Html\Descriptor\TitleLink::__construct
	 */
	public function testConstructor() {
		$context = $this->createMock( IContextSource::class );
		$config = $this->createMock( Config::class );

		$link = new TitleLink( $context, $config );

		$this->assertInstanceOf( ILink::class, $link );
	}

	/**
	 * @covers \BlueSpice\Html\Descriptor\TitleLink::getCSSClasses
	 */
	public function testExistingTitle() {
		$context = $this->createMock( IContextSource::class );
		$config = $this->createMock( Config::class );

		$title = $this->createMock( Title::class );
		$title->method( 'exists' )->willReturn( true );
		$title->method( 'isExternal' )->willReturn( false );

		$link = new TitleLink( $context, $config, $title );

		$this->assertNotContains( 'new', $link->getCSSClasses() );
	}

	/**
	 * @covers \BlueSpice\Html\Descriptor\TitleLink::getCSSClasses
	 */
	public function testNotExistingTitle() {
		$context = $this->createMock( IContextSource::class );
		$config = $this->createMock( Config::class );

		$title = $this->createMock( Title::class );
		$title->method( 'exists' )->willReturn( false );
		$title->method( 'isExternal' )->willReturn( false );

		$link = new TitleLink( $context, $config, $title );

		$this->assertContains( 'new', $link->getCSSClasses() );
	}
}
