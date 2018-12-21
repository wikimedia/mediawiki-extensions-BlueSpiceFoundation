<?php

namespace BlueSpice\Hook;

use BlueSpice\Hook;

abstract class BSApiStoreBaseBeforeReturnData extends Hook {
	/**
	 * @var \BlueSpice\Api\Store
	 */
	protected $store;
	/**
	 * @var \BlueSpice\Data\ResultSet
	 */
	protected $resultSet;
	/**
	 * @var \BlueSpice\Data\Schema
	 */
	protected $schema;

	/**
	 * @param \BlueSpice\Api\Store $store
	 * @param \BlueSpice\Data\ResultSet $resultSet
	 * @param \BlueSpice\Data\Schema $schema
	 * @return boolean
	 */
	public static function callback( $store, &$resultSet, &$schema ) {
		$className = static::class;
		$handler = new $className(
			null,
			null,
			$store,
			$resultSet,
			$schema
		);
		return $handler->doProcess();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \BlueSpice\Api\Store $store
	 * @param \BlueSpice\Data\ResultSet $resultSet
	 * @param \BlueSpice\Data\Schema $schema
	 */
	public function __construct( $context, $config, $store, &$resultSet, &$schema ) {
		parent::__construct( $context, $config );

		$this->store = $store;
		$this->resultSet = &$resultSet;
		$this->schema = &$schema;
	}
}
