<?php

namespace BlueSpice\RunJobsTriggerHandler;

interface IRunConditionChecker {
	/**
	 *
	 * @param \BlueSpice\IRunJobsTriggerHandler $runJobsTriggerHandler
	 * @param string $regKey
	 * @return boolean
	 */
	public function shouldRun( $runJobsTriggerHandler, $regKey );
}
