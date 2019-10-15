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
 * For further information visit https://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice;

use Config;

/**
 * EntityConfigFactory class for BlueSpice
 * @package BlueSpiceFoundation
 */
class EntityConfigFactory {
	/**
	 *
	 * @var EntityConfig[]
	 */
	protected $entityConfigs = null;

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @var ExtensionAttributeBasedRegistry
	 */
	protected $entityRegistry = null;

	/**
	 * @param ExtensionAttributeBasedRegistry $entityRegistry
	 * @param Config $config
	 */
	public function __construct( ExtensionAttributeBasedRegistry $entityRegistry,
		Config $config ) {
		$this->entityRegistry = $entityRegistry;
		$this->config = $config;
	}

	/**
	 * EntityConfig factory
	 * @param string $type - Entity type
	 * @return EntityConfig - or null
	 */
	public function newFromType( $type ) {
		if ( $this->entityConfigs ) {
			if ( !isset( $this->entityConfigs[$type] ) ) {
				return null;
			}
			return $this->entityConfigs[$type];
		}
		$this->entityConfigs = [];
		$defaults = [];

		// Deprecated: This hook should not be used anymore - Use the bluespice
		// global config mechanism instead
		\Hooks::run( 'BSEntityConfigDefaults', [ &$defaults ] );
		foreach ( $this->entityRegistry->getAllKeys() as $key ) {
			$configClass = $this->entityRegistry->getValue( $key );
			$this->entityConfigs[$key] = new $configClass(
				$this->config,
				$key,
				// deprecated
				$defaults
			);
		}

		if ( !isset( $this->entityConfigs[$type] ) ) {
			return null;
		}
		return $this->entityConfigs[$type];
	}
}
