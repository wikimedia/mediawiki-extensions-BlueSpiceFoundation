<?php

namespace BlueSpice\Tests\RunJobsTriggerHandler\Interval;

use BlueSpice\RunJobsTriggerHandler\Interval\OnceADay;

class OnceADayTest extends \PHPUnit\Framework\TestCase {
	public function testCurrentDay() {
		OnceADay::resetInstanceCounter();

		$currentTS = new \DateTime( '1970-01-01' );
		$expectedNextTS = new \DateTime( '1970-01-01 01:00:00' );

		$interval = new OnceADay();
		$nextTS = $interval->getNextTimestamp( $currentTS );

		$this->assertEquals( $expectedNextTS, $nextTS, 'Should be same day' );
	}

	public function testNextDay() {
		OnceADay::resetInstanceCounter();

		$currentTS = new \DateTime( '1970-01-01 02:00:00' );
		$expectedNextTS = new \DateTime( '1970-01-02 01:00:00' );

		$interval = new OnceADay();
		$nextTS = $interval->getNextTimestamp( $currentTS );

		$this->assertEquals( $expectedNextTS, $nextTS, 'Should be next day' );
	}

	public function testMultiInstanceSpreading() {
		OnceADay::resetInstanceCounter();

		$currentTS = new \DateTime( '1970-01-01' );
		$expectedNextTS1 = new \DateTime( '1970-01-01 01:00:00' );
		$expectedNextTS2 = new \DateTime( '1970-01-01 01:15:00' );
		$expectedNextTS3 = new \DateTime( '1970-01-01 01:30:00' );

		$interval1 = new OnceADay();
		$nextTS1 = $interval1->getNextTimestamp( $currentTS );

		$interval2 = new OnceADay();
		$nextTS2 = $interval2->getNextTimestamp( $currentTS );

		$interval3 = new OnceADay();
		$nextTS3 = $interval3->getNextTimestamp( $currentTS );

		$this->assertEquals( $expectedNextTS1, $nextTS1, 'Should be unshifted' );
		$this->assertEquals( $expectedNextTS2, $nextTS2, 'Should be shifted by 15 minutes' );
		$this->assertEquals( $expectedNextTS3, $nextTS3, 'Should be shifted by 30 minutes' );
	}
}
