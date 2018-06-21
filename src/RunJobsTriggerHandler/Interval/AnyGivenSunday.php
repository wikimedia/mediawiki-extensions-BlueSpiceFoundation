<?php

namespace BlueSpice\RunJobsTriggerHandler\Interval;

use BlueSpice\RunJobsTriggerHandler\Interval;

class AnyGivenSunday implements Interval {

	/**
	 *
	 * @param \DateTime $currentRunTimestamp
	 * @return \DateTime
	 */
	public function getNextTimestamp( $currentRunTimestamp ) {
		$nextSunday = clone $currentRunTimestamp;
		$nextSunday->modify( 'next sunday' );
		return $nextSunday;
	}
}
