<?php

namespace BlueSpice;

interface IRunJobsTriggerHandler {

	/**
	 * @return  \Status
	 */
	public function run();

	/**
	 * @return RunJobsTriggerHandler\Interval
	 */
	public function getInterval();
}
