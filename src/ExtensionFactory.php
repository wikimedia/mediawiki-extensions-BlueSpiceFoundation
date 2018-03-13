<?php
/**
 * ExtensionFactory class for BlueSpice
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

class ExtensionFactory {
	/**
	 *
	 * @var Extension[]
	 */
	protected $extensions = [];

	protected $allExtensionsLoaded = false;

	/**
	 *
	 * @var \BlueSpice\ExtensionRegistry
	 */
	protected $extensionRegistry = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 * @param \BlueSpice\ExtensionRegistry $extensionRegistry
	 * @param \Config $config
	 * @return Extension | null
	 */
	public function __construct( $extensionRegistry, $config ) {
		$this->extensionRegistry = $extensionRegistry;
		$this->config = $config;
	}

	protected function factory( $name, $definition ) {
		if( isset( $this->extensions[$name] ) ) {
			return $this->extensions[$name];
		}
		$class = $definition['className'];
		if( strpos( $class, "\\" ) !== 0 ) {
			$class = "\\$class";
		}

		if( !class_exists( $class ) ) {
			//this may change in the future, as there is not much left, that
			//would be written into the extensin classes
			throw new \BsException(
				"Class $class for Extension $name not found!"
			);
		}

		$this->extensions[$name] = new $class(
			$definition,
			\RequestContext::getMain(),
			$this->config
		);

		$this->legacyFactory( $name, $this->extensions[$name] );
		return $this->extensions[$name];
	}

	protected function legacyFactory( $name, $extension ) {
		if( !$extension instanceof \BsExtensionMW ) {
			return;
		}

		$core = \BsCore::getInstance();
		//this is for extensions using the old mechanism and may have their
		//own __constructor
		$extension->setConfig( $this->config );
		$extension->setContext(
			\RequestContext::getMain()
		);
		$extension->setCore( $core );
		$extension->setup(
			$name,
			$this->extensionRegistry->getExtensionDefinitionByName( $name )
		);
		return;
	}

	/**
	 * Returns all instances of registerd BlueSpice extension
	 * @return Extension[]
	 */
	public function getExtensions() {
		if( $this->allExtensionsLoaded ) {
			return $this->extensions;
		}
		$definitions = $this->extensionRegistry->getExtensionDefinitions();
		foreach( $definitions as $name => $definition ) {
			if( $name === 'BlueSpiceFoundation' ) {
				continue;
			}
			$this->factory( $name, $definition );
		}
		return $this->extensions;
	}

	/**
	 * Returns an instance of the requested BlueSpice extension or null, when
	 * not found
	 * @param string $name
	 * @return Extension | null
	 */
	public function getExtension( $name ) {
		$extensions = $this->getExtensions();
		if( isset( $extensions[$name] ) ) {
			return $extensions[$name];
		}
		return null;
	}

	/**
	 * Returns a list of all running BlueSpice extensions
	 * @return array
	 */
	public function getExtensionNames() {
		return array_keys( $this->getExtensions() );
	}

	/**
	 * Provides an array of inforation sets about all registered extensions
	 * @return array
	 */
	public function getExtensionInformation() {
		$info = [];
		foreach ( $this->getExtensions() as $name => $extension ) {
			$info[$name] = $extension->getInfo();
		}

		return $info;
	}
}
