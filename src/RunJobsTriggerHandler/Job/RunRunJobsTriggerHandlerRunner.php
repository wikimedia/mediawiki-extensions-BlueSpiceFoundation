<?php

namespace BlueSpice\RunJobsTriggerHandler\Job;

use BlueSpice\RunJobsTriggerRunner;
use ConfigException;
use Job;
use Title;

class RunRunJobsTriggerHandlerRunner extends Job {
	/**
	 * Constructor
	 */
	public function __construct() {
		$dummyTitle = Title::newFromText( 'RunJobsTriggerHandlerRunner' );
		parent::__construct( 'runRunJobsTriggerHandlerRunner', $dummyTitle );
	}

	/**
	 * Run the job
	 * @return bool Success
	 * @throws ConfigException
	 */
	public function run() {
		return RunJobsTriggerRunner::run();
	}
}
