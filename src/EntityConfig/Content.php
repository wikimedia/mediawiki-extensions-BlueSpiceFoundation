<?php

namespace BlueSpice\EntityConfig;

use BlueSpice\Data\Entity\Schema;
use BlueSpice\Entity\Content as ContentEntity;
use BlueSpice\EntityConfig;
use MWStake\MediaWiki\Component\DataStore\FieldType;

abstract class Content extends EntityConfig {

	/**
	 *
	 * @return string
	 */
	protected function get_ContentClass() {
		return "\\BlueSpice\\Content\\Entity";
	}

	/**
	 *
	 * @return array
	 */
	protected function get_AttributeDefinitions() {
		return array_merge( parent::get_AttributeDefinitions(), [
			ContentEntity::ATTR_TIMESTAMP_CREATED => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::DATE,
				Schema::INDEXABLE => true,
				Schema::STORABLE => false,
			],
			ContentEntity::ATTR_TIMESTAMP_TOUCHED => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::DATE,
				Schema::INDEXABLE => true,
				Schema::STORABLE => false,
			],
		] );
	}
}
