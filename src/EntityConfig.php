<?php

/**
 * EntityConfig class for BlueSpice
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
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
 * For further information visit http://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */
namespace BlueSpice;
use BlueSpice\Data\Entity\Schema;
use BlueSpice\Data\FieldType;

/**
 * EntityConfig class for BlueSpice
 * @package BlueSpiceFoundation
 */
abstract class EntityConfig implements \JsonSerializable {
	protected static $aEntityConfigs = null;
	protected static $aDefaults = array();
	protected $sType = '';

	/**
	 * EntityConfig factory
	 * @param string $sType - Entity type
	 * @return EntityConfig - or null
	 */
	public static function factory( $sType ) {
		if( !is_null( static::$aEntityConfigs ) ) {
			if( !isset( static::$aEntityConfigs[$sType] ) ) {
				return null;
			}
			return static::$aEntityConfigs[$sType];
		}
		//TODO: Check params and classes
		$aRegisteredEntities = EntityRegistry::getRegisteredEntities();
		foreach( $aRegisteredEntities as $sKey => $sConfigClass ) {
			static::$aEntityConfigs[$sKey] = new $sConfigClass();
			static::$aEntityConfigs[$sKey]->sType = $sKey;
			array_merge(
				static::$aDefaults,
				static::$aEntityConfigs[$sKey]->addGetterDefaults()
			);
		}
		\Hooks::run( 'BSEntityConfigDefaults', [ &static::$aDefaults ] );
		return static::$aEntityConfigs[$sType];
	}


	protected static function getDefault( $sOption ) {
		if( !isset( static::$aDefaults[$sOption] ) ) {
			return false;
		}
		return static::$aDefaults[$sOption];
	}

	/**
	 * Getter for config methods
	 * @param string $sOption
	 * @return mixed - The return value of the internaly called method or the
	 * default
	 */
	public function get( $sOption ) {
		$sMethod = "get_$sOption";
		if( !is_callable( array($this, $sMethod) ) ) {
			return static::getDefault( $sOption );
		}
		return $this->$sMethod();
	}

	/**
	 * Returns a json serializable object
	 * @return stdClass
	 */
	public function jsonSerialize() {
		$aConfig = array();
		foreach( get_class_methods( $this ) as $sMethod ) {
			if( strpos($sMethod, 'get_') !== 0 ) {
				continue;
			}
			//remove the get_
			$sVarName = substr( $sMethod, 4 );
			$aConfig[$sVarName] = $this->$sMethod();
		}
		return (object) array_merge(
			static::$aDefaults,
			$aConfig
		);
	}

	/**
	 * @return string - EntityConfig type
	 */
	public function getType() {
		return $this->sType;
	}

	abstract protected function addGetterDefaults();
	abstract protected function get_EntityClass();

	protected function get_ContentClass() {
		return "\\BlueSpice\\Content\\Entity";
	}

	protected function get_AttributeDefinitions() {
		$attributeDefinitions =  [
			'id' => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::INT,
				Schema::INDEXABLE => true,
				Schema::STORABLE => true,
			],
			'ownerid' => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::INT,
				Schema::INDEXABLE => true,
				Schema::STORABLE => true,
			],
			'type' => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::STRING,
				Schema::INDEXABLE => true,
				Schema::STORABLE => true,
			],
			'archived' => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::BOOLEAN,
				Schema::INDEXABLE => true,
				Schema::STORABLE => true,
			],
			'parentid' => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::INT,
				Schema::INDEXABLE => true,
				Schema::STORABLE => true,
			],
			'timestampcreated' => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::DATE,
				Schema::INDEXABLE => true,
				Schema::STORABLE => false,
			],
			'timestamptouched' => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::DATE,
				Schema::INDEXABLE => true,
				Schema::STORABLE => false,
			],
		];

		\Hooks::run( 'BSEntityConfigAttributeDefinitions', [
			$this,
			&$attributeDefinitions,
		]);

		return $attributeDefinitions;
	}
}
