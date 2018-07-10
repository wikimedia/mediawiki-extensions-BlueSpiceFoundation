<?php

namespace BlueSpice;

use Skin;
use Wikimedia\Rdbms\LoadBalancer;
use BlueSpice\Services;
use Wikimedia\Rdbms\IDatabase;
use Config;
use IContextSource;
use User;

abstract class AlertProviderBase implements IAlertProvider {

	/**
	 *
	 * @var LoadBalancer
	 */
	protected $loadBalancer = null;

	/**
	 *
	 * @var Skin
	 */
	protected $skin = null;

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @param Skin $skin
	 * @param LoadBalancer $loadBalancer
	 * @param Config $config
	 */
	public function __construct( $skin, $loadBalancer, $config ) {
		$this->skin = $skin;
		$this->loadBalancer = $loadBalancer;
		$this->config = $config;
	}

	/**
	 *
	 * @param Skin $skin
	 * @return IAlertProvider
	 */
	public static function factory( $skin ) {
		$services = Services::getInstance();

		$loadBalancer = $services->getDBLoadBalancer();
		$config = $services->getConfigFactory()->makeConfig( 'bsg' );

		$instance = new static( $skin, $loadBalancer, $config );

		return $instance;
	}

	/**
	 *
	 * @param int $type
	 * @return IDatabase
	 */
	protected function getDB( $type = DB_REPLICA ) {
		return $this->loadBalancer->getConnection( $type );
	}

	/**
	 *
	 * @return Config
	 */
	protected function getConfig() {
		return $this->config;
	}

	/**
	 *
	 * @return User
	 */
	protected function getUser() {
		return $this->skin->getUser();
	}
}
