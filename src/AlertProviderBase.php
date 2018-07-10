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
	 * @param Skin $skin
	 * @param LoadBalancer $loadBalancer
	 */
	public function __construct( $skin, $loadBalancer ) {
		$this->skin = $skin;
		$this->loadBalancer = $loadBalancer;
	}

	/**
	 *
	 * @param Skin $skin
	 * @return IAlertProvider
	 */
	public static function factory( $skin ) {
		$loadBalancer = Services::getInstance()->getDBLoadBalancer();
		$instance = new static( $skin, $loadBalancer );

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
		return $this->skin->getConfig();
	}

	/**
	 *
	 * @return User
	 */
	protected function getUser() {
		return $this->skin->getUser();
	}
}
