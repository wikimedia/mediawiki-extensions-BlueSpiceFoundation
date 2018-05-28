<?php

namespace BlueSpice\Tests\Hook\SoftwareInfo;

class AddBlueSpiceTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {

		$context = $this->getMockBuilder( '\RequestContext' )
			->disableOriginalConstructor()
			->getMock();

		$config = $this->getMockBuilder( '\HashConfig' )
			->disableOriginalConstructor()
			->getMock();

		$software = [];


		$this->assertInstanceOf(
			'\BlueSpice\Hook\SoftwareInfo\AddBlueSpice',
			new \BlueSpice\Hook\SoftwareInfo\AddBlueSpice(
				$context,
				$config,
				$software
			)
		);
	}

	public function testProcess() {

		$context = $this->getMockBuilder( '\RequestContext' )
			->disableOriginalConstructor()
			->getMock();

		$config = new \HashConfig( [
			'BlueSpiceExtInfo' => [
				'name' => 'BlueSpice SUPER',
				'version' => '5.9.1'
			]
		] );

		$software = [];

		$instance = new \BlueSpice\Hook\SoftwareInfo\AddBlueSpice(
			$context,
			$config,
			$software
		);
		$instance->process();

		$this->assertArrayHasKey( '5.9.1', array_flip( $software ) );
	}
}
