<?php

namespace BlueSpice\Data\Watchlist;

use BlueSpice\Data\FieldType;

class Schema extends \BlueSpice\Data\Schema {
	public function __construct() {
		parent::__construct( [
			Record::USER_ID => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::INT
			],
			Record::USER_DISPLAY_NAME => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::STRING
			],
			/**
			 * Creating a link is expensive and the result is not filterable by
			 * standard filters. Still they are important as hooks may modify
			 * their content (e.g. by providing data attributes or other) and
			 * they can contain additional information (e.g. redlink).
			 * Therefore links always get created _after_ filtering and paging!
			 */
			Record::USER_LINK => [
				self::FILTERABLE => false,
				self::SORTABLE => false,
				self::TYPE => FieldType::STRING
			],
			Record::PAGE_ID => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::INT
			],
			Record::PAGE_PREFIXED_TEXT => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::STRING
			],
			Record::PAGE_LINK => [
				self::FILTERABLE => false,
				self::SORTABLE => false,
				self::TYPE => FieldType::STRING
			],
			Record::NOTIFICATIONTIMESTAMP => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::DATE
			],
			Record::HAS_UNREAD_CHANGES => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::BOOLEAN
			],
			Record::IS_TALK_PAGE => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::BOOLEAN
			],
		]);
	}
}
