<?php

namespace BlueSpice\RunJobsTriggerHandler;

interface Interval {
	/**
	 *
	 * @param \DateTime $currentRunTimestamp
	 * @return \DateTime
	 */
	public function getNextTimestamp( $currentRunTimestamp );
}
