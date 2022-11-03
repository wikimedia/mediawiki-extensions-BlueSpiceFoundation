<?php

namespace BlueSpice\Tests\Installer;

require_once __DIR__ . '/../../../src/Installer/AutoExtensionHandler.php';

use BlueSpice\Installer\AutoExtensionHandler;
use PHPUnit\Framework\TestCase;

class AutoExtensionHandlerTest extends TestCase {

	/**
	 * @covers AutoExtensionHandler::getAutoExtensionData
	 * @return void
	 */
	public function testGetAutoExtensionData() {
		$handler = new AutoExtensionHandler( __DIR__ . '/data' );
		$actualExtensionNames = $handler->getExtensions();
		$expectedExtensionNames = [
			'ext-Extension1' => 'Extension1',
			'ext-Extension3' => 'Extension3',
			'ext-Extension6' => 'Extension6',
			'ext-Extension8' => 'Extension8',
			'ext-Extension10' => 'Extension10',
			'ext-Extension11' => 'Extension11',
			'ext-Skin1' => 'Skin1',
			'ext-Skin3' => 'Skin3',
			'ext-Skin6' => 'Skin6'
		];
		$this->assertEquals( $expectedExtensionNames, $actualExtensionNames );
	}

}
