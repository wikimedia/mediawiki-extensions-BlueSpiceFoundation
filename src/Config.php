<?php

namespace BlueSpice;

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

	/**
	 *
	 * @param \LoadBalancer $loadBalancer
	 */
	public function __construct( $loadBalancer ) {
		$this->loadBalancer = $loadBalancer;
		parent::__construct( [
			new \GlobalVarConfig( 'bsgOverride' ),
			$this->makeDatabaseConfig(),
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

	protected function makeDatabaseConfig() {
		$hash = [];
		$dbr = $this->loadBalancer->getConnection( DB_REPLICA );
		//when config get initialized before the database is created
		//(f.e.: in update.php)
		//TODO: Cache this config
		if( !$dbr->tableExists( 'bs_settings3' ) ) {
			return new \HashConfig( $hash );
		}
		$res = $dbr->select( 'bs_settings3', '*' );

		foreach( $res as $row ) {
			if( strpos(  $row->s_name, 'bsg' ) === 0 ) {
				$name = substr( $row->s_name, 3 );
				$hash[$name] = \FormatJson::decode( $row->s_value );
			}
		}

		return new \HashConfig( $hash );
	}

}
