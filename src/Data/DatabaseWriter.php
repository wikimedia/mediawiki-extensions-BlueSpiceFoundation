<?php

namespace BlueSpice\Data;

abstract class DatabaseWriter implements IWriter {

	/**
	 *
	 * @var \Wikimedia\Rdbms\IDatabase
	 */
	protected $db = null;

	/**
	 *
	 * @param \LoadBalancer $loadBalancer
	 */
	public function __construct( $loadBalancer ) {
		$this->db = $loadBalancer->getConnection( DB_MASTER );
	}
}
