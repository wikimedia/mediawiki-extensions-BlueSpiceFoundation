<?php

namespace BlueSpice\Data\User;

use BlueSpice\Data\FieldType;

class Schema extends \BlueSpice\Data\Schema {
	public function __construct() {
		parent::__construct( [
			Record::ID => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::INT
			],
			Record::USER_NAME => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::STRING
			],
			Record::USER_REAL_NAME => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::STRING
			],
		]);
	}
}
