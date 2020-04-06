<?php

namespace BlueSpice\Data\Settings;

use BlueSpice\Data\FieldType;

class Schema extends \BlueSpice\Data\Schema {
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
