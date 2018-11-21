<?php

namespace BlueSpice\RunJobsTriggerHandler\Interval;

use BlueSpice\RunJobsTriggerHandler\Interval;

class OnceEveryHour implements Interval {

	/**
	 *
	 * @param \DateTime $currentRunTimestamp
	 * @param array $options
	 * @return \DateTime
	 */
	public function getNextTimestamp( $currentRunTimestamp, $options ) {
		$nextTS = clone $currentRunTimestamp;
		$nextTS->modify( '+1 hour' );
		$nextTS->setTime( $nextTS->format( 'H' ), 0, 0 );

		return $nextTS;
	}
}
