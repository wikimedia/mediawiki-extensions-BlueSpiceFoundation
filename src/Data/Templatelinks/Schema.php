<?php

namespace BlueSpice\Data\Templatelinks;

use MWStake\MediaWiki\Component\DataStore\FieldType;

class Schema extends \MWStake\MediaWiki\Component\DataStore\Schema {
	public function __construct() {
		parent::__construct( [
			Record::TEMPLATE_TITLE => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::STRING
			],
			Record::TEMPLATE_NS_ID => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::INT
			],
			Record::PAGE_ID => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::INT
			],
			/**
			 * Creating a link is expensive and the result is not filterable by
			 * standard filters. Still they are important as hooks may modify
			 * their content (e.g. by providing data attributes or other) and
			 * they can contain additional information (e.g. redlink).
			 * Therefore links always get created _after_ filtering and paging!
			 */
			Record::TEMPLATE_LINK => [
				self::FILTERABLE => false,
				self::SORTABLE => false,
				self::TYPE => FieldType::STRING
			]
		] );
	}
}
