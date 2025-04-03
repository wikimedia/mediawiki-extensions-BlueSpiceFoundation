<?php

namespace BlueSpice\Data\Watchlist;

use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\DataStore\DatabaseReader;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;
use Wikimedia\Rdbms\LoadBalancer;

class Reader extends DatabaseReader {

	/**
	 *
	 * @var bool
	 */
	protected $filterForContextUser = false;

	/**
	 *
	 * @param LoadBalancer $loadBalancer
	 * @param IContextSource|null $context
	 * @param Config|null $config
	 * @param bool $filterForContextUser
	 */
	public function __construct( $loadBalancer, ?IContextSource $context = null,
			?Config $config = null, $filterForContextUser = false ) {
		parent::__construct( $loadBalancer, $context, $config );
		$this->filterForContextUser = $filterForContextUser;
	}

	/**
	 *
	 * @param ReaderParams $params
	 * @return PrimaryDataProvider
	 */
	protected function makePrimaryDataProvider( $params ) {
		$contentNamespaces = MediaWikiServices::getInstance()
			->getNamespaceInfo()
			->getContentNamespaces();
		$contextUser = null;
		if ( $this->filterForContextUser ) {
			$contextUser = $this->context->getUser();
		}

		return new PrimaryDataProvider( $this->db, $contentNamespaces, $contextUser );
	}

	/**
	 *
	 * @return SecondaryDataProvider
	 */
	protected function makeSecondaryDataProvider() {
		return new SecondaryDataProvider(
			MediaWikiServices::getInstance()->getLinkRenderer(),
			$this->context
		);
	}

	/**
	 *
	 * @return Schema
	 */
	public function getSchema() {
		return new Schema();
	}
}
