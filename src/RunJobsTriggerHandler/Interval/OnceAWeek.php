<?php

namespace BlueSpice\RunJobsTriggerHandler\Interval;

use BlueSpice\RunJobsTriggerHandler\Interval;

class OnceAWeek implements Interval {

	/**
	 *
	 * @param \DateTime $currentRunTimestamp
	 * @param array $options
	 * @return \DateTime
	 */
	public function getNextTimestamp( $currentRunTimestamp, $options ) {
		$nextSunday = clone $currentRunTimestamp;
		$nextSunday->modify( 'next ' . $options['once-a-week-day'] );
		return $nextSunday;
	}
}
