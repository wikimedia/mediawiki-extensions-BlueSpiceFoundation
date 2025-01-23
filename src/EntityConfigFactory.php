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
 * @author     Patric Wirth
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice;

use MediaWiki\Config\Config;
use MediaWiki\MediaWikiServices;

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

		$services = MediaWikiServices::getInstance();
		// Deprecated: This hook should not be used anymore - Use the bluespice
		// global config mechanism instead
		$services->getHookContainer()->run( 'BSEntityConfigDefaults', [
			&$defaults
		] );
		foreach ( $this->entityRegistry->getAllKeys() as $key ) {
			$callable = $this->entityRegistry->getValue( $key );
			if ( !is_callable( $callable ) ) {
				// deprecated since 3.1.1
				$this->entityConfigs[$key] = new $callable(
					$this->config,
					$key,
					// deprecated
					$defaults
				);
				wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
				continue;
			}
			$instance = call_user_func_array( $callable, [
				$this->config,
				$key,
				$services
			] );
			if ( !$instance ) {
				continue;
			}
			$this->entityConfigs[$key] = $instance;
		}

		if ( !isset( $this->entityConfigs[$type] ) ) {
			return null;
		}
		return $this->entityConfigs[$type];
	}
}
