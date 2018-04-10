<?php

namespace BlueSpice;

use BlueSpice\IRunJobsTriggerHandler;
use BlueSpice\RunJobsTriggerHandler\Interval\OnceADay;

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
	 *
	 * @var INotifier
	 */
	protected $notifier = null;

	/**
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
	 * @param \Config $config
	 * @param INotifier $notifier
	 * @return IRunJobsTriggerHandler
	 */
	public static function factory( $config, $loadBalancer, $notifier ) {
		$className = static::class;
		return new $className( $config, $loadBalancer, $notifier );
	}

	/**
	 *
	 * @param \Config $config
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
	 * @param INotifier $notifier
	 */
	public function __construct( $config, $loadBalancer, $notifier ) {
		$this->config = $config;
		$this->loadBalancer = $loadBalancer;
		$this->notifier = $notifier;
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

	/**
	 *
	 * @return RunJobsTriggerHandler\Interval
	 */
	public function getInterval() {
		return new OnceADay();
	}
}
