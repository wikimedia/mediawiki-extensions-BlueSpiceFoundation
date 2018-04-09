<?php

namespace BlueSpice\RunJobsTriggerHandler\Interval;

use BlueSpice\RunJobsTriggerHandler\Interval;

class EveryYearOn1stOfApril implements Interval {

	/**
	 *
	 * @param \DateTime $currentRunTimestamp
	 * @return \DateTime
	 */
	public function getNextTimestamp( $currentRunTimestamp ) {
		$the1stOfApril = new \DateTime();
		$the1stOfApril->setDate( $the1stOfApril->format( 'Y' ), 4, 1 );

		if( $the1stOfApril > $currentRunTimestamp ) {
			return $the1stOfApril;
		}

		$the1stOfApril->modify( '+1 year' );

		return $the1stOfApril;
	}
}
