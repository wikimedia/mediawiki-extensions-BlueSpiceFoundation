<?php

namespace BlueSpice\Data\Settings;

use BlueSpice\Data\FieldType;

class Schema extends \BlueSpice\Data\Schema {
	public function __construct() {
		parent::__construct( [
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
		]);
	}
}
