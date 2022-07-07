<?php

namespace BlueSpice\Data;

use MWStake\MediaWiki\Component\DataStore\DatabaseReader as Base;

/**
 * @deprecated since 4.2. Use mediawiki-component-datastore
 */
abstract class DatabaseReader extends Base implements IReader {
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
