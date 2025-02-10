<?php

namespace BlueSpice\Tests\Hook\HtmlPageLinkRendererEnd;

use MediaWiki\Config\HashConfig;
use MediaWiki\Context\RequestContext;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;

class AddDataTitleTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @covers \BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataTitle::__construct
	 */
	public function testCanConstruct() {
		$context = $this->getMockBuilder( RequestContext::class )
			->disableOriginalConstructor()
			->getMock();

		$config = $this->getMockBuilder( HashConfig::class )
			->disableOriginalConstructor()
			->getMock();

		$linkRenderer = MediaWikiServices::getInstance()->getLinkRenderer();
		$title = Title::newMainPage();
		$isKnown = true;
		$html = new \HtmlArmor( '' );
		$attribs = [];
		$ret = '';

		$this->assertInstanceOf(
			'\BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataTitle',
			new \BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataTitle(
				$context,
				$config,
				$linkRenderer,
				$title,
				$isKnown,
				$html,
				$attribs,
				$ret
			)
		);
	}

	/**
	 * @covers \BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataTitle::process
	 */
	public function testProcess() {
		$GLOBALS[ 'BS_ADD_DATA_TITLE_TEST' ] = true;
		$context = $this->getMockBuilder( RequestContext::class )
			->disableOriginalConstructor()
			->getMock();

		$config = $this->getMockBuilder( HashConfig::class )
			->disableOriginalConstructor()
			->getMock();

		$linkRenderer = MediaWikiServices::getInstance()->getLinkRenderer();
		$title = Title::newMainPage();
		$html = new \HtmlArmor( '' );
		$isKnown = true;
		$attribs = [];
		$ret = '';

		$instance = new \BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataTitle(
			$context,
			$config,
			$linkRenderer,
			$title,
			$isKnown,
			$html,
			$attribs,
			$ret
		);
		$instance->process();
		unset( $GLOBALS[ 'BS_ADD_DATA_TITLE_TEST' ] );

		$this->assertArrayHasKey( 'data-bs-title', $attribs );
		$this->assertEquals( $title->getPrefixedDBkey(), $attribs['data-bs-title'] );
	}
}
