<?php

namespace BlueSpice\Tests\LinkEnd;

class AddDataTitleTest extends \PHPUnit_Framework_TestCase {

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
			'\BlueSpice\Hooks\LinkEnd\AddDataTitle',
			new \BlueSpice\Hooks\LinkEnd\AddDataTitle(
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

	public function testProcess() {

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

		$instance = new \BlueSpice\Hooks\LinkEnd\AddDataTitle(
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

		$this->assertArrayHasKey( 'data-bs-title', $attribs );
		$this->assertEquals( $title->getPrefixedDBkey(), $attribs['data-bs-title'] );
	}
}