<?php

namespace BlueSpice\Tests\RunJobsTriggerHandler\Interval;

use BlueSpice\RunJobsTriggerHandler\Interval\OnceADay;

class OnceADayTest extends \PHPUnit\Framework\TestCase {
	/**
	 * @covers BlueSpice\RunJobsTriggerHandler\Interval\OnceADay::getNextTimestamp
	 */
	public function testCurrentDay() {
		OnceADay::resetInstanceCounter();

		$currentTS = new \DateTime( '1970-01-01' );
		$expectedNextTS = new \DateTime( '1970-01-01 01:00:00' );

		$interval = new OnceADay();
		$nextTS = $interval->getNextTimestamp( $currentTS, [
			'basetime' => [ 1, 0, 0 ]
		] );

		$this->assertEquals( $expectedNextTS, $nextTS, 'Should be same day' );
	}

	/**
	 * @covers BlueSpice\RunJobsTriggerHandler\Interval\OnceADay::getNextTimestamp
	 */
	public function testNextDay() {
		OnceADay::resetInstanceCounter();

		$currentTS = new \DateTime( '1970-01-01 02:00:00' );
		$expectedNextTS = new \DateTime( '1970-01-02 01:00:00' );

		$interval = new OnceADay();
		$nextTS = $interval->getNextTimestamp( $currentTS, [
			'basetime' => [ 1, 0, 0 ]
		] );

		$this->assertEquals( $expectedNextTS, $nextTS, 'Should be next day' );
	}

	/**
	 * @covers BlueSpice\RunJobsTriggerHandler\Interval\OnceADay::getNextTimestamp
	 */
	public function testMultiInstanceSpreading() {
		OnceADay::resetInstanceCounter();

		$currentTS = new \DateTime( '1970-01-01' );
		$options = [
			'basetime' => [ 1, 0, 0 ]
		];
		$expectedNextTS1 = new \DateTime( '1970-01-01 01:00:00' );
		$expectedNextTS2 = new \DateTime( '1970-01-01 01:15:00' );
		$expectedNextTS3 = new \DateTime( '1970-01-01 01:30:00' );

		$interval1 = new OnceADay();
		$nextTS1 = $interval1->getNextTimestamp( $currentTS, $options );

		$interval2 = new OnceADay();
		$nextTS2 = $interval2->getNextTimestamp( $currentTS, $options );

		$interval3 = new OnceADay();
		$nextTS3 = $interval3->getNextTimestamp( $currentTS, $options );

		$this->assertEquals( $expectedNextTS1, $nextTS1, 'Should be unshifted' );
		$this->assertEquals( $expectedNextTS2, $nextTS2, 'Should be shifted by 15 minutes' );
		$this->assertEquals( $expectedNextTS3, $nextTS3, 'Should be shifted by 30 minutes' );
	}

	/**
	 * @covers BlueSpice\RunJobsTriggerHandler\Interval\OnceADay::getNextTimestamp
	 */
	public function testBasetimeOverride() {
		OnceADay::resetInstanceCounter();

		$currentTS = new \DateTime( '1970-01-01 02:00:00' );
		$expectedNextTS = new \DateTime( '1970-01-01 05:00:00' );

		$interval = new OnceADay();
		$nextTS = $interval->getNextTimestamp( $currentTS, [
			'basetime' => [ 5, 0, 0 ]
		] );

		$this->assertEquals( $expectedNextTS, $nextTS, 'Should have respect configured basetime' );
	}
}
