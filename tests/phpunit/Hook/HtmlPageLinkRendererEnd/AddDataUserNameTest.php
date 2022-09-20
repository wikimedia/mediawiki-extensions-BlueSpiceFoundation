<?php

namespace BlueSpice\Tests\Hook\HtmlPageLinkRendererEnd;

use MediaWiki\MediaWikiServices;

class AddDataUserNameTest extends \MediaWikiIntegrationTestCase {
	protected $testUserName = 'wiki Sysöp';

	/**
	 * @covers \BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataUserName::__construct
	 */
	public function testCanConstruct() {
		$context = $this->getMockBuilder( '\RequestContext' )
			->disableOriginalConstructor()
			->getMock();

		$config = $this->getMockBuilder( '\HashConfig' )
			->disableOriginalConstructor()
			->getMock();

		$linkRenderer = MediaWikiServices::getInstance()->getLinkRenderer();
		$title = \Title::newMainPage();
		$isKnown = true;
		$html = new \HtmlArmor( '' );
		$attribs = [];
		$ret = '';

		$this->assertInstanceOf(
			'\BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataUserName',
			new \BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataUserName(
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
	 * @covers \BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataUserName::process
	 */
	public function testProcessUserPageWithRename() {
		define( 'BS_ADD_DATA_USER_NAME_TEST', true );
		$services = MediaWikiServices::getInstance();
		$testuser = $services->getUserFactory()->newFromName( $this->testUserName );
		$testuser->addToDatabase();
		$testuser->setRealName( 'Sysöp, W. Iki' );
		$testuser->saveSettings();

		$context = $this->getMockBuilder( '\RequestContext' )
			->disableOriginalConstructor()
			->getMock();

		$config = $this->getMockBuilder( '\HashConfig' )
			->disableOriginalConstructor()
			->getMock();

		$linkRenderer = $services->getLinkRenderer();
		$title = \Title::makeTitle( NS_USER, 'Wiki Sysöp' );
		$isKnown = true;
		$html = $newHtml = new \HtmlArmor( $title->getText() );
		$attribs = [];
		$ret = '';

		$instance = new \BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataUserName(
			$context,
			$config,
			$linkRenderer,
			$title,
			$isKnown,
			$newHtml,
			$attribs,
			$ret
		);
		$instance->process();

		$this->assertArrayHasKey( 'data-bs-username', $attribs );
		$this->assertEquals( $testuser->getName(), $attribs['data-bs-username'] );
		$this->assertEquals(
			$testuser->getRealName(),
			\HtmlArmor::getHtml( $newHtml )
		);
	}

	/**
	 * @covers \BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataUserName::process
	 */
	public function testProcessUserPageWithOutRename() {
		$services = MediaWikiServices::getInstance();
		$testuser = $services->getUserFactory()->newFromName( $this->testUserName );
		$testuser->addToDatabase();
		$testuser->setRealName( 'Sysöp, W. Iki' );
		$testuser->saveSettings();

		$context = $this->getMockBuilder( '\RequestContext' )
			->disableOriginalConstructor()
			->getMock();

		$config = $this->getMockBuilder( '\HashConfig' )
			->disableOriginalConstructor()
			->getMock();

		$linkRenderer = $services->getLinkRenderer();
		$title = \Title::makeTitle( NS_USER, 'Wiki Sysöp' );
		$isKnown = true;
		$html = $newHtml = new \HtmlArmor( 'Some link text' );
		$attribs = [];
		$ret = '';

		$instance = new \BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataUserName(
			$context,
			$config,
			$linkRenderer,
			$title,
			$isKnown,
			$newHtml,
			$attribs,
			$ret
		);
		$instance->process();

		$this->assertArrayHasKey( 'data-bs-username', $attribs );
		$this->assertEquals( $testuser->getName(), $attribs['data-bs-username'] );
		$this->assertEquals(
			\HtmlArmor::getHtml( $html ),
			\HtmlArmor::getHtml( $newHtml )
		);
	}

	/**
	 * @covers \BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataUserName::process
	 */
	public function testProcessNoProcess() {
		$context = $this->getMockBuilder( '\RequestContext' )
			->disableOriginalConstructor()
			->getMock();

		$config = $this->getMockBuilder( '\HashConfig' )
			->disableOriginalConstructor()
			->getMock();

		$linkRenderer = MediaWikiServices::getInstance()->getLinkRenderer();
		$title = \Title::makeTitle( NS_MAIN, 'Not a user page' );
		$isKnown = true;
		$html = $newHtml = new \HtmlArmor( 'Some link text' );
		$attribs = [];
		$ret = '';

		$instance = new \BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataUserName(
			$context,
			$config,
			$linkRenderer,
			$title,
			$isKnown,
			$newHtml,
			$attribs,
			$ret
		);
		$instance->process();

		$this->assertArrayNotHasKey( 'data-bs-username', $attribs );
		$this->assertEquals(
			\HtmlArmor::getHtml( $html ),
			\HtmlArmor::getHtml( $newHtml )
		);
	}
}
