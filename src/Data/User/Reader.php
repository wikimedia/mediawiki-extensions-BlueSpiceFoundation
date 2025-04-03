<?php

namespace BlueSpice\Data\User;

use MediaWiki\Context\IContextSource;
use MWStake\MediaWiki\Component\DataStore\DatabaseReader;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;
use Wikimedia\Rdbms\LoadBalancer;

class Reader extends DatabaseReader {
	/**
	 *
	 * @param LoadBalancer $loadBalancer
	 * @param IContextSource|null $context
	 */
	public function __construct( $loadBalancer, ?IContextSource $context = null ) {
		parent::__construct( $loadBalancer, $context, $context->getConfig() );
	}

	/**
	 *
	 * @param ReaderParams $params
	 * @return PrimaryDataProvider
	 */
	protected function makePrimaryDataProvider( $params ) {
		return new PrimaryDataProvider( $this->db );
	}

	/**
	 *
	 * @return null
	 */
	protected function makeSecondaryDataProvider() {
		return null;
	}

	/**
	 *
	 * @return Schema
	 */
	public function getSchema() {
		return new Schema();
	}

}
