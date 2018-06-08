<?php

namespace BlueSpice\Job;

use MediaWiki\Logger\LoggerFactory;

class RunJobsTrigger extends \Job {
	protected $oEntity = null;

	const JOBCOMMAND = 'runjobstrigger';

	public function run() {
		$registry = new \BlueSpice\ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationRunJobsTriggerHandlerRegistry'
		);

		$logger = LoggerFactory::getInstance( 'runjobs-trigger-runner' );

		$runConditionChecker = new \BlueSpice\RunJobsTriggerHandler\JSONFileBasedRunConditionChecker(
			new \DateTime(),
			BSDATADIR,
			$logger
		);

		$runner = new \BlueSpice\RunJobsTriggerRunner(
			$registry,
			$logger,
			$runConditionChecker,
			\MediaWiki\MediaWikiServices::getInstance()
				->getMainConfig(),
			\LBFactory::singleton()->getMainLB(),
			\BlueSpice\NotifierFactory::newNotifier() //TODO: Add NotifierFactory to BlueSpice\Services
		);

		$runner->execute();
	}

	/**
	 *
	 * @param Title $oTitle
	 * @param array $params
	 */
	public function __construct( $oTitle = null, $params = [] ) {
		parent::__construct(
			static::JOBCOMMAND,
			$oTitle,
			$params
		);
	}
}