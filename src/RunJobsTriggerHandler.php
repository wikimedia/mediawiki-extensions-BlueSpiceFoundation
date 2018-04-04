<?php

namespace BlueSpice;

use BlueSpice\IRunJobsTriggerHandler;

abstract class RunJobsTriggerHandler implements IRunJobsTriggerHandler {

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
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
	 * @param \Config $config
	 * @return IRunJobsTriggerHandler
	 */
	public static function factory( $config, $loadBalancer ) {
		$className = static::class;
		return new $className( $config, $loadBalancer );
	}

	/**
	 *
	 * @param \Config $config
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
	 */
	public function __construct( $config, $loadBalancer ) {
		$this->config = $config;
		$this->loadBalancer = $loadBalancer;
	}

	/**
	 *
	 * @return \Status
	 */
	public function run() {
		return $this->doRun();
	}

	/**
	 * @return \Status
	 */
	protected abstract function doRun();
}
