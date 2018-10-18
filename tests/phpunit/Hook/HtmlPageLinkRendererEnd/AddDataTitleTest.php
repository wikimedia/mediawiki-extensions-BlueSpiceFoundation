<?php

namespace BlueSpice\Tests\Hook\HtmlPageLinkRendererEnd;

class AddDataTitleTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {

		$context = $this->getMockBuilder( '\RequestContext' )
			->disableOriginalConstructor()
			->getMock();

		$config = $this->getMockBuilder( '\HashConfig' )
			->disableOriginalConstructor()
			->getMock();

		$linkRenderer = \MediaWiki\MediaWikiServices::getInstance()
			->getLinkRenderer();
		$title = \Title::newMainPage();
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

	public function testProcess() {
		define( 'BS_ADD_DATA_TITLE_TEST', true );
		$context = $this->getMockBuilder( '\RequestContext' )
			->disableOriginalConstructor()
			->getMock();

		$config = $this->getMockBuilder( '\HashConfig' )
			->disableOriginalConstructor()
			->getMock();

		$linkRenderer = \MediaWiki\MediaWikiServices::getInstance()
			->getLinkRenderer();
		$title = \Title::newMainPage();
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

		$this->assertArrayHasKey( 'data-bs-title', $attribs );
		$this->assertEquals( $title->getPrefixedDBkey(), $attribs['data-bs-title'] );
	}
}
