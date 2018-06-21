<?php

namespace BlueSpice\RunJobsTriggerHandler\Interval;

use BlueSpice\RunJobsTriggerHandler\Interval;

class TwiceADay implements Interval {

	/**
	 *
	 * @param \DateTime $currentRunTimestamp
	 * @return \DateTime
	 */
	public function getNextTimestamp( $currentRunTimestamp ) {
		$firstTS = clone $currentRunTimestamp;
		$firstTS->setTime( 1, 0, 0 );

		$secondTS = clone $currentRunTimestamp;
		$secondTS->setTime( 13, 0, 0 );

		if( $firstTS > $currentRunTimestamp ) {
			return $firstTS;
		}

		if( $firstTS < $currentRunTimestamp
			&& $currentRunTimestamp < $secondTS ) {
			return $secondTS;
		}

		if( $currentRunTimestamp > $secondTS ) {
			$firstTS->modify( '+1 day' );
			return $firstTS;
		}
	}
}
