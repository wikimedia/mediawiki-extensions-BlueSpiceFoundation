<?php

namespace BlueSpice\Data\Entity;

use BlueSpice\EntityConfig;
use BlueSpice\ExtensionAttributeBasedRegistry;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\DataStore\FieldType;

class Schema extends \MWStake\MediaWiki\Component\DataStore\Schema {
	public const STORABLE = 'storeable';
	public const INDEXABLE = 'indexable';

	/**
	 *
	 * @return array
	 */
	protected function getDefaultFieldDefinition() {
		return [
			static::FILTERABLE => true,
			static::SORTABLE => true,
			static::TYPE => FieldType::STRING,
			static::STORABLE => true,
			static::INDEXABLE => true,
		];
	}

	/**
	 *
	 * @param array $fieldDefinition
	 * @return array
	 */
	protected function fillMissingWithDefaults( $fieldDefinition ) {
		foreach ( $this->getDefaultFieldDefinition() as $key => $defaultVal ) {
			if ( array_key_exists( $key, $fieldDefinition ) ) {
				continue;
			}
			$fieldDefinition[$key] = $defaultVal;
		}
		return $fieldDefinition;
	}

	/**
	 *
	 * @return EntityConfig[]
	 */
	protected function getEntityConfigs() {
		$entityConfigs = [];
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationEntityRegistry'
		);
		$configFactory = MediaWikiServices::getInstance()->getService(
			'BSEntityConfigFactory'
		);
		foreach ( $registry->getAllKeys() as $type ) {
			$entityConfig = $configFactory->newFromType( $type );
			if ( !$entityConfig ) {
				continue;
			}
			$entityConfigs[] = $entityConfig;
		}
		return $entityConfigs;
	}

	public function __construct() {
		$scheme = [];
		foreach ( $this->getEntityConfigs() as $entityConfig ) {
			$definitions = $entityConfig->get( 'AttributeDefinitions' );
			foreach ( $definitions as $key => $definition ) {
				$definitions[$key] = $this->fillMissingWithDefaults(
					$definition
				);
			}
			$scheme = array_merge( $scheme, $definitions );
		}
		parent::__construct( $scheme );
	}

	/**
	 * @return string[]
	 */
	public function getIndexableFields() {
		return $this->filterFields( static::INDEXABLE, true );
	}

	/**
	 * @return string[]
	 */
	public function getStorableFields() {
		return $this->filterFields( static::STORABLE, true );
	}

	/**
	 * @return string[]
	 */
	public function getUnindexableFields() {
		return $this->filterFields( static::INDEXABLE, false );
	}

	/**
	 * @return string[]
	 */
	public function getUnstorableFields() {
		return $this->filterFields( static::STORABLE, false );
	}
}
