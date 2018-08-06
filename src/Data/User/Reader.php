<?php

namespace BlueSpice\Data\User;

use \BlueSpice\Data\DatabaseReader;
use \Wikimedia\Rdbms\LoadBalancer;

class Reader extends DatabaseReader {
	/**
	 *
	 * @param LoadBalancer $loadBalancer
	 * @param \IContextSource|null $context
	 */
	public function __construct( $loadBalancer, \IContextSource $context = null ) {
		parent::__construct( $loadBalancer, $context, $context->getConfig() );
	}

	protected function makePrimaryDataProvider( $params ) {
		return new PrimaryDataProvider( $this->db );
	}

	protected function makeSecondaryDataProvider() {
		return null;
	}

	public function getSchema() {
		return new Schema();
	}

}
