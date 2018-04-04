<?php

namespace BlueSpice;

class RunJobsTriggerRunner {

	/**
	 *
	 * @var IRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @var \Wikimedia\Rdbms\LoadBalancer
	 */
	protected $loadBalancer = null;

	/**
	 *
	 * @param IRegistry $registry
	 * @param \Config $config
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
	 */
	public function __construct( $registry, $config, $loadBalancer ) {
		$this->registry = $registry;
		$this->config = $config;
		$this->loadBalancer = $loadBalancer;
	}

	public function execute() {
		$factoryKeys = $this->registry->getAllKeys();
		foreach( $factoryKeys as $regKey ) {
			$factoryCallback = $this->registry->getValue( $regKey );
			$triggerHandler = call_user_func_array(
				$factoryCallback,
				[
					$this->config,
					$this->loadBalancer
				]
			);

			if( $triggerHandler instanceof IRunJobsTriggerHandler === false ) {
				throw new Exception(
					"RunJobsTriggerHanlder factory '$regKey' did not return "
						. "'IRunJobsTriggerHandler' instance!"
				);
			}

			$status = $triggerHandler->run();
			//TODO: reflect returned status and maybe write to Logfile
		}
	}
}
