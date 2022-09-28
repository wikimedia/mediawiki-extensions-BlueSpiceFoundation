<?php

namespace BlueSpice;

use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\RunJobsTrigger\Handler;

abstract class RunJobsTriggerHandler extends Handler {

	/**
	 *
	 * @var INotifier
	 */
	protected $notifier = null;

	/** @var MediaWikiServices */
	protected $services = null;

	/**
	 * @param \Config $config
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
	 * @param INotifier|null $notifier
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
		parent::__construct( $config, $loadBalancer );
		$this->notifier = $notifier;
		$this->services = MediaWikiServices::getInstance();
	}
}
