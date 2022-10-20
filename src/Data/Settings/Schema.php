<?php

namespace BlueSpice\Data\Settings;

use MWStake\MediaWiki\Component\DataStore\FieldType;

class Schema extends \MWStake\MediaWiki\Component\DataStore\Schema {
	/**
	 * @param array|null $input
	 */
	public function __construct( $input = [] ) {
		parent::__construct( array_merge( [
			Record::NAME => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::STRING
			],
			Record::VALUE => [
				self::FILTERABLE => false,
				self::SORTABLE => false,
				self::TYPE => FieldType::MIXED
			],
		], $input ) );
	}
}
