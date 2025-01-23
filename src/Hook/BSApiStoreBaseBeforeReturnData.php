<?php

namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MWStake\MediaWiki\Component\DataStore\ResultSet;
use MWStake\MediaWiki\Component\DataStore\Schema;

abstract class BSApiStoreBaseBeforeReturnData extends Hook {
	/**
	 * @var \BlueSpice\Api\Store
	 */
	protected $store;
	/**
	 * @var ResultSet
	 */
	protected $resultSet;
	/**
	 * @var Schema
	 */
	protected $schema;

	/**
	 * @param \BlueSpice\Api\Store $store
	 * @param ResultSet &$resultSet
	 * @param Schema &$schema
	 * @return bool
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
	 * @param IContextSource $context
	 * @param Config $config
	 * @param \BlueSpice\Api\Store $store
	 * @param ResultSet &$resultSet
	 * @param Schema &$schema
	 */
	public function __construct( $context, $config, $store, &$resultSet, &$schema ) {
		parent::__construct( $context, $config );

		$this->store = $store;
		$this->resultSet = &$resultSet;
		$this->schema = &$schema;
	}
}
