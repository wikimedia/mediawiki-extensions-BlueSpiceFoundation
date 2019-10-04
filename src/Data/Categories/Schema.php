<?php

namespace BlueSpice\Data\Categories;

use BlueSpice\Data\FieldType;
use BlueSpice\Data\Schema as SchemaBase;

class Schema extends SchemaBase {
	public function __construct() {
		parent::__construct( [
			Record::CAT_ID => [
				self::FILTERABLE => false,
				self::SORTABLE => true,
				self::TYPE => FieldType::INT
			],
			Record::CAT_TITLE => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::STRING
			],
			Record::CAT_LINK => [
				self::FILTERABLE => false,
				self::SORTABLE => false,
				self::TYPE => FieldType::STRING
			],
			Record::CAT_PAGES => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::INT
			],
			Record::CAT_SUBCATS => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::INT
			],
			Record::CAT_FILES => [
				self::FILTERABLE => false,
				self::SORTABLE => false,
				self::TYPE => FieldType::INT
			]
		] );
	}
}
