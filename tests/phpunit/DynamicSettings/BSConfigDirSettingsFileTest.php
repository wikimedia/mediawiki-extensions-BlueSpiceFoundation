<?php

namespace BlueSpice\Tests\DynamicSettings;

use BlueSpice\DynamicSettings\BSConfigDirSettingsFile;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class BSConfigDirSettingsFileTest extends TestCase {

	/**
	 *
	 * @param string $testConfigDir
	 * @param string $tmpfile
	 * @return LoggerInterface
	 */
	private function makeMock( $testConfigDir, $tmpfile = 'test-settings.php' ) {
		$loggerMock = $this->createMock( LoggerInterface::class );

		$mock = $this->getMockBuilder( BSConfigDirSettingsFile::class )
		->setConstructorArgs( [ $loggerMock, $testConfigDir ] )
		->getMockForAbstractClass();

		$mock->expects( $this->any() )
		->method( 'getFilename' )
		->will( $this->returnValue( $tmpfile ) );

		return $mock;
	}

	/**
	 * @covers BSConfigDirSettingsFile::apply
	 * @return void
	 */
	public function testApply() {
		$testConfigDir = dirname( __DIR__ ) . '/data/config/';
		$mock = $this->makeMock( $testConfigDir );

		$GLOBALS['bsgTestSetting'] = 'To be overwritten';
		$locals = [];
		$mock->apply( $locals );

		$this->assertEquals( 'Test', $GLOBALS['bsgTestSetting'] );
	}

	/**
	 * @covers BSConfigDirSettingsFile::setData
	 * @return void
	 */
	public function testSetData() {
		$testConfigDir = wfTempDir();
		$testConfigFilename = microtime();
		$mock = $this->makeMock( $testConfigDir, $testConfigFilename );

		$mock->setData( 'Some data' );
		$mock->persist();

		$fileData = file_get_contents( "$testConfigDir/$testConfigFilename" );

		$this->assertEquals( 'Some data', $fileData );
	}

	/**
	 * @covers BSConfigDirSettingsFile::persist
	 * @return void
	 */
	public function testPersist() {
		$testConfigDir = wfTempDir() . '/' . microtime();
		$testConfigFilename = microtime();
		$mock = $this->makeMock( $testConfigDir, $testConfigFilename );

		$mock->persist();

		$this->assertFileExists( "$testConfigDir/$testConfigFilename" );
	}

	/**
	 * @covers BSConfigDirSettingsFile::fetch
	 * @return void
	 */
	public function testFetch() {
		$testConfigDir = dirname( __DIR__ ) . '/data/config/';
		$mock = $this->makeMock( $testConfigDir );
		$expectedFileContent = <<<HERE
<?php

\$GLOBALS['bsgTestSetting'] = 'Test';

HERE;

		$this->assertEquals( $expectedFileContent, $mock->fetch() );
	}
}
