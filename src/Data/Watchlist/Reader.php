<?php

namespace BlueSpice\Data\Watchlist;

use BlueSpice\Data\ReaderParams;
use BlueSpice\Data\DatabaseReader;
use MWNamespace;
use IContextSource;
use Config;
use Wikimedia\Rdbms\LoadBalancer;

class Reader extends DatabaseReader {

	/**
	 *
	 * @var boolean
	 */
	protected $filterForContextUser = false;

	/**
	 *
	 * @param LoadBalancer $loadBalancer
	 * @param IContextSource|null $context
	 * @param Config|null $config
	 * @param bool $filterForContextUser
	 */
	public function __construct( $loadBalancer, IContextSource $context = null,
			Config $config = null, $filterForContextUser = false ) {
		parent::__construct( $loadBalancer, $context, $config );
		$this->filterForContextUser = $filterForContextUser;
	}

	/**
	 *
	 * @param ReaderParams $params
	 * @return PrimaryDataProvider
	 */
	protected function makePrimaryDataProvider( $params ) {
		$contentNamespaces = MWNamespace::getContentNamespaces();
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
			\MediaWiki\MediaWikiServices::getInstance()->getLinkRenderer()
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
