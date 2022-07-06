<?php

namespace BlueSpice\Data\Page;

use MWStake\MediaWiki\Component\DataStore\FieldType;

class Schema extends \MWStake\MediaWiki\Component\DataStore\Schema {
	public const TABLE_NAME = 'page';

	public function __construct() {
		parent::__construct( [
			Record::ID => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::INT
			],
			Record::NS => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::INT
			],
			Record::TITLE => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::STRING
			],
			Record::IS_REDIRECT => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::BOOLEAN
			],
			Record::ID_NEW => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::BOOLEAN
			],
			Record::TOUCHED => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::DATE
			],
			Record::LATEST => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::INT
			],
			Record::CONTENT_MODEL => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::STRING
			],
		] );
	}
}
