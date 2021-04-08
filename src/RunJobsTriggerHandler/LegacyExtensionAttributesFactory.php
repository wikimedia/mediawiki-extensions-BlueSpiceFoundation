<?php

namespace BlueSpice\RunJobsTriggerHandler;

use BlueSpice\ExtensionAttributeBasedRegistry;
use BlueSpice\INotifier;
use Config;
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
	 * @var INotifier
	 */
	protected $notifier = null;

	/**
	 * @param Config|null $config
	 * @param LoadBalancer|null $loadBalancer
	 * @param INotifier|null $notifier
	 */
	public function __construct( $config = null, $loadBalancer = null, $notifier = null ) {
		$this->config = $config;
		$this->loadBalancer = $loadBalancer;
		$this->notifier = $notifier;

		$services = MediaWikiServices::getInstance();
		if ( $this->config === null ) {
			$this->config = $services->getConfigFactory()->makeConfig( 'bsg' );
		}
		if ( $this->loadBalancer === null ) {
			$this->loadBalancer = $services->getDBLoadBalancer();
		}
		if ( $this->notifier === null ) {
			$this->notifier = $services->getService( 'BSNotificationManager' )->getNotifier();
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
					$this->loadBalancer,
					$this->notifier
				]
			);

			$this->checkHandlerInterface( $regKey );

			$handlers[$regKey] = $this->currentTriggerHandler;
		}

		return $handlers;
	}
}
