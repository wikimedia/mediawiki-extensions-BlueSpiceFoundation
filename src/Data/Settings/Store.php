<?php

namespace BlueSpice\Data\Settings;

use MediaWiki\Context\IContextSource;
use MWStake\MediaWiki\Component\DataStore\IStore;
use WANObjectCache;
use Wikimedia\Rdbms\LoadBalancer;

class Store implements IStore {

	/**
	 * @var IContextSource
	 */
	protected $context = null;

	/** @var LoadBalancer */
	protected $loadBalancer = null;

	/** @var WANObjectCache */
	private $cache;

	/**
	 * @param IContextSource $context
	 * @param LoadBalancer $loadBalancer
	 * @param WANObjectCache|null $cache
	 */
	public function __construct( $context, $loadBalancer, ?WANObjectCache $cache = null ) {
		$this->context = $context;
		$this->loadBalancer = $loadBalancer;
		$this->cache = $cache;
		if ( !$this->cache ) {
			$this->cache = \MediaWiki\MediaWikiServices::getInstance()->getMainWANObjectCache();
		}
	}

	/**
	 * @return Reader
	 */
	public function getReader() {
		return new Reader( $this->loadBalancer, $this->context, $this->cache );
	}

	/**
	 * @return Writer
	 */
	public function getWriter() {
		return new Writer(
			$this->getReader(),
			$this->loadBalancer,
			$this->context
		);
	}
}
