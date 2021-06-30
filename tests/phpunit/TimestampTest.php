<?php

namespace BlueSpice\Tests;

use BlueSpice\Timestamp;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BlueSpice\Timestamp
 */
class TimestampTest extends TestCase {

	/**
	 * @return array
	 */
	public function provideTestTimestampData() {
		$currentTimestamp = '2021-06-30 00:58:47';

		return [
			'1-week-ago' => [
				$currentTimestamp,
				'2021-06-23 00:58:47',
				'one week ago'
			],
			'1-week-6-days-ago' => [
				$currentTimestamp,
				'2021-06-17 00:58:47',
				'one week and 6 days ago'
			],
			'2-weeks-ago' => [
				$currentTimestamp,
				'2021-06-16 00:58:47',
				'2 weeks ago'
			],
			'1-month-4-weeks-ago' => [
				$currentTimestamp,
				'2021-05-01 00:58:47',
				'one month and 4 weeks ago'
			],
			'2-months-ago' => [
				$currentTimestamp,
				'2021-04-30 00:58:47',
				'2 months ago'
			]
		];
	}

	/**
	 * @covers \BlueSpice\Timestamp::getAgeString()
	 * @dataProvider provideTestTimestampData
	 *
	 * @param string $currentTimestamp
	 * @param string $pastTimestamp
	 * @param string $expectedAgeString
	 * @return void
	 */
	public function testAgeString( $currentTimestamp, $pastTimestamp, $expectedAgeString ) {
		$pastTimestampObj = Timestamp::getInstance( $pastTimestamp );
		$currentTimestampObj = Timestamp::getInstance( $currentTimestamp );

		$ageString = $pastTimestampObj->getAgeString( $currentTimestampObj );

		$this->assertEquals( $expectedAgeString, $ageString );
	}

}
