<?php

/**
 * EntityRegistry class for BlueSpice
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
 * For further information visit http://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice;
use MediaWiki\MediaWikiServices;

/**
 * EntityRegistry class for BlueSpice
 * @package BlueSpiceFoundation
 */
class EntityRegistry {
	protected $entitydefinitions = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @param type $config
	 */
	public function __construct( $config ) {
		$this->config = $config;
	}

	protected function runRegister( $bForceReload = false ) {
		if( $this->entitydefinitions && !$bForceReload ) {
			return true;
		}

		$extRegistry = \ExtensionRegistry::getInstance();
		$this->entitydefinitions = $extRegistry->getAttribute(
			'BlueSpiceFoundationEntityRegistry'
		);

		//This hook is deprecated - Use attributes mechanism in extension.json
		//to register entities
		\Hooks::run( 'BSEntityRegister', [&$this->entitydefinitions] );

		return true;
	}

	/**
	 * Returns all registered entities ( type => EntityConfigClass )
	 * @return array
	 */
	public function getEntityDefinitions() {
		if( !$this->runRegister() ) {
			return [];
		}
		return $this->entitydefinitions;
	}

	/**
	 * Checks if given type is a registered Entity
	 * @param string $sType
	 * @return bool
	 */
	public function hasType( $sType ) {
		return in_array(
			$sType,
			$this->getTypes()
		);
	}

	/**
	 * Returns a registered entity by given type
	 * @param string $sType
	 * @return array
	 */
	public function getEntityByType( $sType ) {
		if( !$this->hasType( $sType ) ) {
			return [];
		}
		return $this->entitydefinitions[$sType];
	}

	/**
	 * Returns all registered entity types
	 * @return array
	 */
	public function getTypes() {
		return array_keys(
			$this->getEntityDefinitions()
		);
	}

	/**
	 * Returns all registered entities ( type => EntityConfigClass )
	 * @deprecated since version 3.0.0 - User $instance->getEntityDefinitions() instead
	 * @return array
	 */
	public static function getRegisteredEntities() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$entityRegistry = MediaWikiServices::getInstance()->getService(
			'BSEntityRegistry'
		);
		return $entityRegistry->getEntityDefinitions();
	}

	/**
	 * Checks if given type is a registered Entity
	 * @deprecated since version 3.0.0 - User $instance->hasType() instead
	 * @param string $sType
	 * @return bool
	 */
	public static function isRegisteredType( $sType ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$entityRegistry = MediaWikiServices::getInstance()->getService(
			'BSEntityRegistry'
		);
		return $entityRegistry->hasType( $sType );
	}

	/**
	 * Returns a registered entity by given type
	 * @deprecated since version 3.0.0 - User $instance->getEntityByType()
	 * instead
	 * @param string $sType
	 * @return array
	 */
	public static function getRegisteredEntityByType( $sType ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$entityRegistry = MediaWikiServices::getInstance()->getService(
			'BSEntityRegistry'
		);
		return $entityRegistry->getEntityByType( $sType );
	}

	/**
	 * Returns all registered entity types
	 * @deprecated since version 3.0.0 - User $instance->getTypes()
	 * @return array
	 */
	public static function getRegisterdTypeKeys() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$entityRegistry = MediaWikiServices::getInstance()->getService(
			'BSEntityRegistry'
		);
		return $entityRegistry->getTypes();
	}
}
