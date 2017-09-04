<?php

namespace BlueSpice\Data;

abstract class DatabaseReader implements IReader {

	/**
	 *
	 * @var \DatabaseBase
	 */
	protected $db = null;

	/**
	 *
	 * @param \LoadBalancer $loadBalancer
	 */
	public function __construct( $loadBalancer ) {
		$this->db = $loadBalancer->getConnection( DB_REPLICA );
	}
}
