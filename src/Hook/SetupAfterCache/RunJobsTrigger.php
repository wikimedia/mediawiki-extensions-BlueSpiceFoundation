<?php

namespace BlueSpice\Hook\SetupAfterCache;

use BlueSpice\Hook\SetupAfterCache;
use BlueSpice\ExtensionAttributeBasedRegistry;
use BlueSpice\IRunJobsTriggerHandler;
use MediaWiki\Logger\LoggerFactory;
use BlueSpice\RunJobsTriggerHandler\JSONFileBasedRunConditionChecker;
use BlueSpice\NotifierFactory;

class RunJobsTrigger extends SetupAfterCache {

	protected function skipProcessing() {
		return !defined( 'MEDIAWIKI_JOB_RUNNER' );
	}

	protected function doProcess() {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationRunJobsTriggerHandlerRegistry'
		);

		$logger = LoggerFactory::getInstance( 'runjobs-trigger-runner' );

		$runConditionChecker = new JSONFileBasedRunConditionChecker(
			new \DateTime(),
			BSDATADIR,
			$logger
		);

		$runner = new \BlueSpice\RunJobsTriggerRunner(
			$registry,
			$logger,
			$runConditionChecker,
			$this->getConfig(),
			$this->getServices()->getDBLoadBalancer(),
			$this->getServices()->getService( 'BSNotifications' )
				->getNotifier( 'nullnotifier' )
		);

		$runner->execute();

		return true;
	}
}
