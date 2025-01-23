<?php

namespace BlueSpice;

use MediaWiki\Config\Config;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\RunJobsTrigger\Handler;

abstract class RunJobsTriggerHandler extends Handler {

	/** @var MediaWikiServices */
	protected $services = null;

	/**
	 * @param Config $config
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
	 * @return IRunJobsTriggerHandler
	 */
	public static function factory( $config, $loadBalancer ) {
		$className = static::class;
		return new $className( $config, $loadBalancer );
	}

	/**
	 *
	 * @param Config $config
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
	 */
	public function __construct( $config, $loadBalancer ) {
		parent::__construct( $config, $loadBalancer );
		$this->services = MediaWikiServices::getInstance();
	}
}
