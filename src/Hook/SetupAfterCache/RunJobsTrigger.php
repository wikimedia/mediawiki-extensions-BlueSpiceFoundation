<?php

namespace BlueSpice\Hook\SetupAfterCache;

use BlueSpice\Hook\SetupAfterCache;
use BlueSpice\ExtensionAttributeBasedRegistry;
use BlueSpice\IRunJobsTriggerHandler;

class RunJobsTrigger extends SetupAfterCache {

	protected function skipProcessing() {
		return !defined( 'MEDIAWIKI_JOB_RUNNER' );
	}

	protected function doProcess() {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationRunJobsTriggerHandlerRegistry'
		);

		$runner = new \BlueSpice\RunJobsTriggerRunner(
			$registry,
			$this->getConfig(),
			$this->getServices()->getDBLoadBalancer()
		);

		$runner->execute();

		return true;
	}
}
