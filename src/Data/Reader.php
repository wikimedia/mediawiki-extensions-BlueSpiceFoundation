<?php

namespace BlueSpice\Data;

use MWStake\MediaWiki\Component\DataStore\Reader as ReaderBase;
use MWStake\MediaWiki\Component\DataStore\ReaderParams as ReaderParams;

/**
 * @deprecated since 4.2. Use mediawiki-component-datastore
 */
abstract class Reader extends ReaderBase implements IReader {
	/**
	 * Compatibility layer - some implementations expect return
	 * to be the deprecated class
	 *
	 * @param ReaderParams $params
	 *
	 * @return ResultSet
	 */
	public function read( $params ) {
		$resultSet = parent::read( $params );
		return new ResultSet( $resultSet->getRecords(), $resultSet->getTotal() );
	}
}
