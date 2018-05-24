<?php

namespace BlueSpice\Tests\RunJobsTriggerHandler;

use BlueSpice\RunJobsTriggerHandler\JSONFileBasedRunConditionChecker as Checker;
use Psr\Log\LoggerInterface;
use BlueSpice\RunJobsTriggerHandler;
use BlueSpice\RunJobsTriggerHandler\Interval\OnceADay;

class JSONFileBasedRunConditionCheckerTest extends \PHPUnit\Framework\TestCase {

	protected $tmpJSONPathname = '';
	protected $fixtureJSONPathname = '';

	protected function setUp() {
		parent::setUp();

		$this->tmpJSONPathname = sys_get_temp_dir() . '/runJobsTriggerData.json';
		$this->fixtureJSONPathname = __DIR__. '/../json/runJobsTriggerData.json';
	}

	public function testShouldRun() {
		$this->ensureTestJSON();

		$currentTS = new \DateTime( '1970-01-01 01:30:00' );
		$dummyLogger = $this->getMockBuilder( LoggerInterface::class )
			->getMock();

		$checker = new Checker(
			$currentTS,
			dirname( $this->tmpJSONPathname ),
			$dummyLogger
		);

		$dummyHandler = $this->getTriggerHandlerMock();

		$this->assertTrue( $checker->shouldRun( $dummyHandler, 'handler1' ) );
		$this->assertFalse( $checker->shouldRun( $dummyHandler, 'handler2' ) );
		$this->assertFalse( $checker->shouldRun( $dummyHandler, 'handler3' ) );
	}

	public function testDataPersistence() {
		$this->ensureTestJSON();
		OnceADay::resetInstanceCounter();

		$currentTS = new \DateTime( '1970-01-01 01:30:00' );
		$dummyLogger = $this->getMockBuilder( LoggerInterface::class )
			->getMock();

		$checker = new Checker(
			$currentTS,
			dirname( $this->tmpJSONPathname ),
			$dummyLogger
		);

		$dummyHandler = $this->getTriggerHandlerMock();

		$this->assertTrue( $checker->shouldRun( $dummyHandler, 'handler1' ) );

		unset( $checker ); //Invoke '__destruct'

		$persistedData = \FormatJson::decode(
			file_get_contents( $this->tmpJSONPathname ),
			true
		);

		$this->assertEquals(
			$persistedData[Checker::DATA_KEY_LASTRUN],
			'19700101013000',
			'Field for "last-run" should be updated'
		);

		$this->assertEquals(
			'19700102010000',
			$persistedData[Checker::DATA_KEY_NEXTRUNS]['handler1'],
			'Field for "next-run" of "handler1" should be updated'
		);

		$this->assertEquals(
			'19700101020000',
			$persistedData[Checker::DATA_KEY_NEXTRUNS]['handler2'],
			'Field for "next-run" of "handler2" should NOT be changed'
		);

		$this->assertEquals(
			'19700101030000',
			$persistedData[Checker::DATA_KEY_NEXTRUNS]['handler3'],
			'Field for "next-run" of "handler3" should NOT be changed'
		);
	}

	protected function ensureTestJSON() {
		copy( $this->fixtureJSONPathname, $this->tmpJSONPathname );
	}

	protected function getTriggerHandlerMock() {
		$mockedMethods[] = 'getInterval';
		$mockedMethods[] = 'doRun';

		$handler = $this->getMockBuilder( RunJobsTriggerHandler::class )
			->setMethods( $mockedMethods )
			->disableOriginalConstructor()
			->getMock();

		$handler->expects( $this->any() )->method( 'getInterval' )
			->willReturn( new OnceADay() );

		$handler->expects( $this->any() )->method( 'doRun' )
			->willReturn( \Status::newGood() );

		return $handler;
	}

}
