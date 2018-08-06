<?php

namespace BlueSpice\Data\Settings;

use \BlueSpice\Data\DatabaseReader;

class Reader extends DatabaseReader {
	/**
	 *
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
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
