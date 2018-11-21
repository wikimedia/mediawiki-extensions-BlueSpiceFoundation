<?php

namespace BlueSpice\RunJobsTriggerHandler;

interface Interval {
	/**
	 *
	 * @param \DateTime $currentRunTimestamp
	 * @param array $options
	 * @return \DateTime
	 */
	public function getNextTimestamp( $currentRunTimestamp, $options );
}
