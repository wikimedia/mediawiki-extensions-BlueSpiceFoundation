<?php

/**
 * EntityConfig class for BlueSpice
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit https://bluespice.com
 *
 * @author     Patric Wirth
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice;

use BlueSpice\Data\Entity\Schema;
use JsonSerializable;
use MediaWiki\Config\Config;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\DataStore\FieldType;

/**
 * EntityConfig class for BlueSpice
 * @package BlueSpiceFoundation
 */
abstract class EntityConfig implements JsonSerializable, Config {

	protected $type = '';

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @var array
	 */
	protected $defaults = [];

	/**
	 *
	 * @param Config $config
	 * @param string $type
	 * @param array $defaults
	 */
	public function __construct( $config, $type = '', $defaults = [] ) {
		$this->config = $config;
		$this->type = $type;
		$this->defaults = array_merge(
			$this->addGetterDefaults(),
			$defaults
		);
	}

	/**
	 *
	 * @param string $sOption
	 * @return mixed
	 */
	protected function getDefault( $sOption ) {
		if ( isset( $this->defaults[$sOption] ) ) {
			return $this->defaults[$sOption];
		}
		return $this->getConfig()->has( $sOption )
			? $this->getConfig()->get( $sOption )
			: false;
	}

	/**
	 *
	 * @return Config
	 */
	final protected function getConfig() {
		if ( $this->config ) {
			return $this->config;
		}
		$this->config = MediaWikiServices::getInstance()
			->getConfigFactory()->makeConfig( 'bsg' );
		return $this->config;
	}

	/**
	 * Getter for config methods
	 * @param string $sOption
	 * @return mixed - The return value of the internaly called method or the
	 * default
	 */
	public function get( $sOption ) {
		$sMethod = "get_$sOption";
		if ( !is_callable( [ $this, $sMethod ] ) ) {
			return $this->getDefault( $sOption );
		}
		return $this->$sMethod();
	}

	/**
	 * check for config methods
	 * @param string $method
	 * @return bool
	 */
	public function has( $method ) {
		$method = "get_$method";
		if ( is_callable( [ $this, $method ] ) ) {
			return true;
		}
		if ( isset( $this->defaults[$method] ) ) {
			return true;
		}
		return $this->getConfig()->has( $method );
	}

	/**
	 * Returns a json serializable object
	 * @return \stdClass
	 */
	public function jsonSerialize(): \stdClass {
		$aConfig = [];
		foreach ( get_class_methods( $this->getConfig() ) as $sMethod ) {
			if ( strpos( $sMethod, 'get_' ) !== 0 ) {
				continue;
			}
			// remove the get_
			$sVarName = substr( $sMethod, 4 );
			$aConfig[$sVarName] = $this->getConfig()->get( $sVarName );
		}
		foreach ( get_class_methods( $this ) as $sMethod ) {
			if ( strpos( $sMethod, 'get_' ) !== 0 ) {
				continue;
			}
			// remove the get_
			$sVarName = substr( $sMethod, 4 );
			$aConfig[$sVarName] = $this->$sMethod();
		}
		return (object)array_merge(
			$this->defaults,
			$aConfig
		);
	}

	/**
	 * @return string - EntityConfig type
	 */
	final public function getType() {
		return $this->type;
	}

	/**
	 *
	 * @return array
	 */
	protected function addGetterDefaults() {
		return [];
	}

	/**
	 *
	 * @return string
	 */
	abstract protected function get_EntityClass();

	/**
	 *
	 * @return string
	 */
	abstract protected function get_StoreClass();

	/**
	 *
	 * @return string
	 */
	protected function get_Renderer() {
		return "entity";
	}

	/**
	 *
	 * @return string
	 */
	protected function get_RenderTemplate() {
		return 'BlueSpiceFoundation.Entity';
	}

	/**
	 *
	 * @return array
	 */
	protected function get_AttributeDefinitions() {
		$attributeDefinitions = [
			Entity::ATTR_ID => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::INT,
				Schema::INDEXABLE => true,
				Schema::STORABLE => true,
			],
			Entity::ATTR_OWNER_ID => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::INT,
				Schema::INDEXABLE => true,
				Schema::STORABLE => true,
			],
			Entity::ATTR_TYPE => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::STRING,
				Schema::INDEXABLE => true,
				Schema::STORABLE => true,
			],
			Entity::ATTR_ARCHIVED => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::BOOLEAN,
				Schema::INDEXABLE => true,
				Schema::STORABLE => true,
			],
			Entity::ATTR_TIMESTAMP_CREATED => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::DATE,
				Schema::INDEXABLE => true,
				Schema::STORABLE => false,
			],
			Entity::ATTR_TIMESTAMP_TOUCHED => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::DATE,
				Schema::INDEXABLE => true,
				Schema::STORABLE => false,
			],
		];

		MediaWikiServices::getInstance()->getHookContainer()->run(
			'BSEntityConfigAttributeDefinitions',
			[
				$this,
				&$attributeDefinitions,
			]
		);

		return $attributeDefinitions;
	}
}
