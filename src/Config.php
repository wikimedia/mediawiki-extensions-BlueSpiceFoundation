<?php

namespace BlueSpice;

use BlueSpice\Data\Settings\Record;
use BlueSpice\Data\Settings\Store;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;

class Config extends \MultiConfig {

	/**
	 *
	 * @var \HashConfig
	 */
	protected $databaseConfig = null;

	/**
	 *
	 * @var \HashConfig
	 */
	protected $overrides = null;

	/**
	 *
	 */
	public function __construct() {
		$this->databaseConfig = $this->makeDatabaseConfig();
		$this->overrides = new \GlobalVarConfig( 'bsgOverride' );
		parent::__construct( [
			$this->overrides,
			&$this->databaseConfig,
			new \GlobalVarConfig( 'bsg' ),
			new \GlobalVarConfig( 'wg' ),
		] );
	}

	/**
	 * Factory method used by \ConfigFactory
	 * @return \Config
	 */
	public static function newInstance() {
		return new self();
	}

	/**
	 * Invalidates the cache of config stored in the database
	 * @return bool
	 */
	public function invalidateCache() {
		$this->databaseConfig = $this->makeDatabaseConfig();
		return true;
	}

	/**
	 *
	 * @return \HashConfig
	 */
	protected function makeDatabaseConfig() {
		$hash = [];
		$store = $this->getStore();
		$resultSet = $store->getReader()->read(
			new ReaderParams( [
				'limit' => ReaderParams::LIMIT_INFINITE
			] )
		);

		foreach ( $resultSet->getRecords() as $record ) {
			$name = $record->get( Record::NAME );
			$hash[ $name ] = $record->get( Record::VALUE );
		}

		return new \HashConfig( $hash );
	}

	/**
	 *
	 * @return Store
	 */
	protected function getStore() {
		return new Store(
			new Context( \RequestContext::getMain(), $this ),
			Services::getInstance()->getDBLoadBalancer()
		);
	}

	/**
	 *
	 * @return \HashConfig
	 */
	public function getOverrides() {
		return $this->overrides;
	}

}
