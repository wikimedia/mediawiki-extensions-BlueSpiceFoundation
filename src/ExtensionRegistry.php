<?php

/**
 * ExtensionRegistry class for BlueSpice
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

/**
 * ExtensionRegistry class for BlueSpice
 * @package BlueSpiceFoundation
 */
class ExtensionRegistry {
	protected $extensionDefinitions = null;

	/**
	 *
	 * @var \ExtensionRegistry
	 */
	protected $extensionRegistry = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @param name $config
	 */
	public function __construct( $extensionRegistry, $config ) {
		$this->extensionRegistry = $extensionRegistry;
		$this->config = $config;
	}

	protected function runRegister() {
		if( $this->extensionDefinitions ) {
			return true;
		}

		$extRegistryV1 = $this->extensionRegistry->getAttribute(
			'bsgExtensions'
		);
		$extRegistryV2 = $this->extensionRegistry->getAttribute(
			'BlueSpiceFoundationExtensions'
		);

		if( empty( $GLOBALS['bsgExtensions'] ) ) {
			$GLOBALS['bsgExtensions'] = [];
		}

		$GLOBALS['bsgExtensions'] = array_merge(
			//old global, wich is still in use in some cases
			$GLOBALS['bsgExtensions'],
			//manifest version 1
			$extRegistryV1,
			//manifest version 2
			$extRegistryV2
		);

		foreach( $GLOBALS['bsgExtensions'] as $name => $definition ) {
			if( $name === 'BlueSpiceFoundation' ) {
				continue;
			}
			$GLOBALS['bsgExtensions'][$name] = $this->makeExtensionDefinition(
				$name,
				$definition
			);
		}
		$this->extensionDefinitions = &$GLOBALS['bsgExtensions'];

		return true;
	}

	protected function makeExtensionDefinition( $name, $definition ) {
		$allThings = $this->extensionRegistry->getAllThings();

		//Some BlueSpice extensions have been registered wrong in the past.
		//The the extension name used as key in bsgExtensions must be equal with
		//the extensions name in the "name" attribute of the extension.json!
		if( !isset( $allThings[$name] ) ) {
			throw new \BsException(
				"$name is not a registered extension!"
			);
		}

		$definition = array_merge(
			$allThings[$name],
			$definition
		);
		if( !isset( $definition['className'] ) ) {
			//this may change in the future, as there is not much left, that
			//would be written into the extensin classes
			throw new \BsException(
				"$name className needs to be set!"
			);
		}
		if( !isset( $definition['extPath'] ) ) {
			$definition['extPath'] = "";
		}
		if( !isset( $definition['status'] ) ) {
			$definition['status'] = "default";
		}
		if( !isset( $definition['package'] ) ) {
			$definition['package'] = "default";
		}

		$extInfo = $this->config->get( 'BlueSpiceExtInfo' );
		$definition['status'] = str_replace(
			'default',
			$extInfo['status'],
			$definition['status']
		);
		$definition['package'] = str_replace(
			'default',
			$extInfo['package'],
			$definition['package']
		);
		return $definition;
	}

	/**
	 * Returns all registered extension definitions
	 * @return array
	 */
	public function getExtensionDefinitions() {
		if( !$this->runRegister() ) {
			return [];
		}
		return $this->extensionDefinitions;
	}

	/**
	 * Checks if given name is a registered Extension
	 * @param string $sName
	 * @return bool
	 */
	public function hasName( $sName ) {
		return in_array(
			$sName,
			$this->getNames()
		);
	}

	/**
	 * Returns a registered extension by given name
	 * @param string $sName
	 * @return array
	 */
	public function getExtensionDefinitionByName( $sName ) {
		if( !$this->hasName( $sName ) ) {
			return [];
		}
		return $this->extensionDefinitions[$sName];
	}

	/**
	 * Returns all registered extensions names
	 * @return array
	 */
	public function getNames() {
		return array_keys(
			$this->getExtensionDefinitions()
		);
	}

}
