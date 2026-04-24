<?php

namespace BlueSpice\Data\Settings;

use MediaWiki\Json\FormatJson;
use MWStake\MediaWiki\Component\DataStore\IPrimaryDataProvider;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;
use WANObjectCache;
use Wikimedia\Rdbms\IDatabase;

class PrimaryDataProvider implements IPrimaryDataProvider {

	/**
	 * @var array
	 */
	protected $data = [];

	/**
	 * @var IDatabase
	 */
	private $db;

	/**
	 * @var WANObjectCache
	 */
	private $cache;

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
			static function () use ( $db ) {
				$data = [];
				$res = $db->select( 'bs_settings3', '*', '', __CLASS__ );
				foreach ( $res as $row ) {
					$data[] = new Record( (object)[
						Record::NAME => $row->s_name,
						Record::VALUE => FormatJson::decode( $row->s_value, true )
					] );
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
