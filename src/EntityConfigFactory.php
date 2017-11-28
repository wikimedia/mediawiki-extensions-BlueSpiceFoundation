<?php

/**
 * EntityConfigFactory class for BlueSpice
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
 * EntityConfigFactory class for BlueSpice
 * @package BlueSpiceFoundation
 */
class EntityConfigFactory {
	protected $entityConfigs = null;

	protected $config = null;
	protected $entityRegistry = null;

	/**
	 * @param \EntityRegistry
	 * @param \Config $config
	 */
	public function __construct( $entityRegistry, $config ) {
		$this->entityRegistry = $entityRegistry;
		$this->config = $config;
	}

	/**
	 * EntityConfig factory
	 * @param string $type - Entity type
	 * @return EntityConfig - or null
	 */
	public function newFromType( $type ) {
		if( $this->entityConfigs ) {
			if( !isset( $this->entityConfigs[$type] ) ) {
				return null;
			}
			return $this->entityConfigs[$type];
		}
		$this->entityConfigs = [];
		//TODO: Check params and classes
		$entityRegistry = MediaWikiServices::getInstance()->getService(
			'BSEntityRegistry'
		);
		$entityDefinitions = $entityRegistry->getEntityDefinitions();
		$defaults = [];

		//Deprecated: This hook should not be used anymore - Use the bluespice
		//global config mechanism instead
		\Hooks::run( 'BSEntityConfigDefaults', [ &$defaults ] );
		foreach( $entityDefinitions as $key => $sConfigClass ) {
			$this->entityConfigs[$key] = new $sConfigClass(
				$this->config,
				$key,
				$defaults //deprecated
			);
		}

		if( !isset( $this->entityConfigs[$type] ) ) {
			return null;
		}
		return $this->entityConfigs[$type];
	}
}
