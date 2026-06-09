<?php

namespace BlueSpice\Data\Settings;

use MediaWiki\Context\IContextSource;
use MWStake\MediaWiki\Component\DataStore\DatabaseReader;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;
use WANObjectCache;

class Reader extends DatabaseReader {

	/** @var WANObjectCache */
	private $cache;

	/**
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
	 * @param IContextSource|null $context
	 * @param WANObjectCache|null $cache
	 */
	public function __construct( $loadBalancer, ?IContextSource $context, ?WANObjectCache $cache = null ) {
		$this->cache = $cache;
		if ( !$this->cache ) {
			$this->cache = \MediaWiki\MediaWikiServices::getInstance()->getMainWANObjectCache();
		}
		parent::__construct( $loadBalancer, $context, $context->getConfig() );
	}

	/**
	 * @param ReaderParams $params
	 * @return PrimaryDataProvider
	 */
	protected function makePrimaryDataProvider( $params ) {
		return new PrimaryDataProvider( $this->db, $this->cache );
	}

	/**
	 * @return null
	 */
	protected function makeSecondaryDataProvider() {
		return null;
	}

	/**
	 * @return Schema
	 */
	public function getSchema() {
		return new Schema();
	}

	/**
	 * @inheritDoc
	 */
	protected function cacheResults( string $hash, array $dataSets ): void {
		// Intentionally do nothing. We cache in a different way in
		// PrimaryDataProvider, to ease cache invalidation
	}
}
