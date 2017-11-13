<?php

namespace BlueSpice;
use BlueSpice\Data\Settings\Store;
use BlueSpice\Data\Settings\Record;
use BlueSpice\Context;

class Config extends \MultiConfig {

	protected $databaseConfig = null;

	public function __construct() {
		$this->databaseConfig = $this->makeDatabaseConfig();
		parent::__construct( [
			new \GlobalVarConfig( 'bsgOverride' ),
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
	 * @return boolean
	 */
	public function invalidateCache() {
		$this->databaseConfig = $this->makeDatabaseConfig();
		return true;
	}

	protected function makeDatabaseConfig() {
		$hash = [];
		$store = $this->getStore();
		$resultSet = $store->getReader()->read(
			new Data\ReaderParams( [
				'limit' => Data\ReaderParams::LIMIT_INFINITE
			] )
		);

		foreach( $resultSet->getRecords() as $record ) {
			$name = $record->get( Record::NAME );
			$hash[ $name ] = $record->get( Record::VALUE );
		}

		return new \HashConfig( $hash );
	}

	protected function getStore() {
		return new Store(
			new Context( \RequestContext::getMain(), $this ),
			\MediaWiki\MediaWikiServices::getInstance()->getDBLoadBalancer()
		);
	}

}
