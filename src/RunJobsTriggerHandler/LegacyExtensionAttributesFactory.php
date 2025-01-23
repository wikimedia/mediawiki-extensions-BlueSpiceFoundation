<?php

namespace BlueSpice\RunJobsTriggerHandler;

use BlueSpice\ExtensionAttributeBasedRegistry;
use MediaWiki\Config\Config;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\RunJobsTrigger\HandlerFactory\Base;
use Wikimedia\Rdbms\LoadBalancer;

class LegacyExtensionAttributesFactory extends Base {

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @var LoadBalancer
	 */
	protected $loadBalancer = null;

	/**
	 * @param Config|null $config
	 * @param LoadBalancer|null $loadBalancer
	 */
	public function __construct( $config = null, $loadBalancer = null ) {
		$this->config = $config;
		$this->loadBalancer = $loadBalancer;

		$services = MediaWikiServices::getInstance();
		if ( $this->config === null ) {
			$this->config = $services->getConfigFactory()->makeConfig( 'bsg' );
		}
		if ( $this->loadBalancer === null ) {
			$this->loadBalancer = $services->getDBLoadBalancer();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function processHandlers( $handlers ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationRunJobsTriggerHandlerRegistry'
		);

		$factoryKeys = $registry->getAllKeys();
		foreach ( $factoryKeys as $regKey ) {
			$factoryCallback = $registry->getValue( $regKey );
			$this->currentTriggerHandler = call_user_func_array(
				$factoryCallback,
				[
					$this->config,
					$this->loadBalancer
				]
			);

			$this->checkHandlerInterface( $regKey );

			$handlers[$regKey] = $this->currentTriggerHandler;
		}

		return $handlers;
	}
}
