<?php

namespace BlueSpice\Data\Settings;

use MediaWiki\Json\FormatJson;
use MWStake\MediaWiki\Component\DataStore\IPrimaryDataProvider;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;
use WANObjectCache;
use Wikimedia\Rdbms\DBError;
use Wikimedia\Rdbms\IDatabase;

class PrimaryDataProvider implements IPrimaryDataProvider {

	/**
	 * @var array
	 */
	protected $data = [];

	/**
	 * @var IDatabase
	 */
	protected $db;

	/**
	 * @var WANObjectCache
	 */
	protected $cache;

	/**
	 * @param IDatabase $db
	 * @param WANObjectCache|null $cache
	 */
	public function __construct( IDatabase $db, ?WANObjectCache $cache = null ) {
		$this->db = $db;
		$this->cache = $cache;
		if ( !$this->cache ) {
			$this->cache = \MediaWiki\MediaWikiServices::getInstance()->getMainWANObjectCache();
		}
	}

	/**
	 * @param ReaderParams $params
	 * @return Record[]
	 */
	public function makeData( $params ) {
		$key = $this->cache->makeKey( 'BlueSpiceFoundation', 'bs_settings3' );
		$db = $this->db;
		$this->data = $this->cache->getWithSetCallback(
			$key,
			$this->cache::TTL_DAY,
			static function ( $oldValue, &$ttl, array &$setOpts ) use ( $db ) {
				$data = [];
				try {
					$res = $db->select( 'bs_settings3', '*', '', __CLASS__ );
					foreach ( $res as $row ) {
						$data[] = new Record( (object)[
							Record::NAME => $row->s_name,
							Record::VALUE => FormatJson::decode( $row->s_value, true )
						] );
					}
				} catch ( DBError $e ) {
					// At the first update run after installation, the table does not yet exist.
					// As run.php calls Setup.php before running the actual maintenance script
					// and triggers this current logic, we do not throw error here.
					wfDebugLog( 'BlueSpiceFoundation', "Error fetching settings: {$e->getMessage()}" );
					// Don't cache the empty result, maybe the table will be created soon.
					$ttl = 0;
					return [];
				}
				return $data;
			}
		);

		return $this->data;
	}

	protected function appendRowToData( \stdClass $row ) {
		$this->data[] = new Record( (object)[
			Record::NAME => $row->s_name,
			Record::VALUE => FormatJson::decode( $row->s_value, true )
		] );
	}
}
