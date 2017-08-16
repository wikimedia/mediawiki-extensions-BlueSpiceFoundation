<?php

namespace BlueSpice\Tests\LinkEnd;

class AddDataUserNameTest extends \MediaWikiTestCase {
	protected $testUserName = 'wiki Sysöp';

	public function testCanConstruct() {

		$context = $this->getMockBuilder( '\RequestContext' )
			->disableOriginalConstructor()
			->getMock();

		$config = $this->getMockBuilder( '\HashConfig' )
			->disableOriginalConstructor()
			->getMock();

		$dummy = new \DummyLinker();
		$title = \Title::newMainPage();
		$options = [];
		$html = '';
		$attribs = [];
		$ret = '';

		$this->assertInstanceOf(
			'\BlueSpice\Hook\LinkEnd\AddDataUserName',
			new \BlueSpice\Hook\LinkEnd\AddDataUserName(
				$context,
				$config,
				$dummy,
				$title,
				$options,
				$html,
				$attribs,
				$ret
			)
		);
	}

	public function testProcessUserPageWithRename() {
		$testuser = \User::newFromName( $this->testUserName );
		$testuser->addToDatabase();
		$testuser->setRealName( 'Sysöp, W. Iki' );
		$testuser->saveSettings();

		$context = $this->getMockBuilder( '\RequestContext' )
			->disableOriginalConstructor()
			->getMock();

		$config = $this->getMockBuilder( '\HashConfig' )
			->disableOriginalConstructor()
			->getMock();

		$dummy = new \DummyLinker();
		$title = \Title::makeTitle( NS_USER, 'Wiki Sysöp' );
		$options = [];
		$html = $title->getText();
		$attribs = [];
		$ret = '';

		$instance = new \BlueSpice\Hook\LinkEnd\AddDataUserName(
			$context,
			$config,
			$dummy,
			$title,
			$options,
			$html,
			$attribs,
			$ret
		);
		$instance->process();

		$this->assertArrayHasKey( 'data-bs-username', $attribs );
		$this->assertEquals( $testuser->getName(), $attribs['data-bs-username'] );
		$this->assertEquals( $testuser->getRealName(), $html );
	}

	public function testProcessUserPageWithOutRename() {
		$testuser = \User::newFromName( $this->testUserName );
		$testuser->addToDatabase();
		$testuser->setRealName( 'Sysöp, W. Iki' );
		$testuser->saveSettings();

		$context = $this->getMockBuilder( '\RequestContext' )
			->disableOriginalConstructor()
			->getMock();

		$config = $this->getMockBuilder( '\HashConfig' )
			->disableOriginalConstructor()
			->getMock();

		$dummy = new \DummyLinker();
		$title = \Title::makeTitle( NS_USER, 'Wiki Sysöp' );
		$options = [];
		$html = 'Some link text';
		$attribs = [];
		$ret = '';

		$instance = new \BlueSpice\Hook\LinkEnd\AddDataUserName(
			$context,
			$config,
			$dummy,
			$title,
			$options,
			$html,
			$attribs,
			$ret
		);
		$instance->process();

		$this->assertArrayHasKey( 'data-bs-username', $attribs );
		$this->assertEquals( $testuser->getName(), $attribs['data-bs-username'] );
		$this->assertEquals( $html, $html );
	}

	public function testProcessNoProcess() {
		$context = $this->getMockBuilder( '\RequestContext' )
			->disableOriginalConstructor()
			->getMock();

		$config = $this->getMockBuilder( '\HashConfig' )
			->disableOriginalConstructor()
			->getMock();

		$dummy = new \DummyLinker();
		$title = \Title::makeTitle( NS_MAIN, 'Not a user page' );
		$options = [];
		$html = 'Some link text';
		$attribs = [];
		$ret = '';

		$instance = new \BlueSpice\Hook\LinkEnd\AddDataUserName(
			$context,
			$config,
			$dummy,
			$title,
			$options,
			$html,
			$attribs,
			$ret
		);
		$instance->process();

		$this->assertArrayNotHasKey( 'data-bs-username', $attribs );
		$this->assertEquals( $html, $html );
	}
}