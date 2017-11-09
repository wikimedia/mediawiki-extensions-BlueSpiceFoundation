<?php

namespace BlueSpice;
use BlueSpice\Data\Settings\Store;
use BlueSpice\Data\Settings\Record;
use BlueSpice\Context;

class Config extends \MultiConfig implements \Serializable {

	/**
	 * This fixes the exception in object cache, when config with loadBalancer
	 * is serialized.
	 * "Database serialization may cause problems, since the connection is not
	 * restored on wakeup"
	 *
	 * @param string $serialized
	 * @return Config
	 */
	public function unserialize( $serialized ) {
		return \MediaWiki\MediaWikiServices::getInstance()
			->getConfigFactory()->makeConfig( 'bsg' );
	}

	/**
	 * This fixes the exception in object cache, when config with loadBalancer
	 * is serialized.
	 * "Database serialization may cause problems, since the connection is not
	 * restored on wakeup"
	 *
	 * @return string
	 */
	public function serialize() {
		return serialize( null );
	}

	/**
	 *
	 * @var \LoadBalancer
	 */
	protected $loadBalancer = null;

	protected $databaseConfig = null;

	/**
	 *
	 * @param \LoadBalancer $loadBalancer
	 */
	public function __construct( $loadBalancer ) {
		$this->loadBalancer = $loadBalancer;
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
		$lb = \MediaWiki\MediaWikiServices::getInstance()->getDBLoadBalancer();
		return new self( $lb );
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
		$dbr = $this->loadBalancer->getConnection( DB_REPLICA );
		if( !$dbr->tableExists( 'bs_settings3' ) ) {
			return new \HashConfig( $hash );
		}
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
			$this->loadBalancer
		);
	}

}
