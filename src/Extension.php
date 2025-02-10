<?php
/**
 * BlueSpice Extension base class
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
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice;

use JsonSerializable;
use MediaWiki\Config\Config;
use MediaWiki\Config\ConfigException;
use MediaWiki\Context\IContextSource;

abstract class Extension extends Context implements JsonSerializable {

	protected $extPath;
	protected $author;
	protected $package;
	protected $status;
	protected $name;
	protected $url;
	protected $version;

	protected $allowedInfoKeys = [
		'extPath' => '',
		'author' => [],
		'package' => '',
		'status' => '',
		'name' => '',
		'url' => '',
		'version' => ''
	];

	public function jsonSerialize(): array {
		return $this->getInfo();
	}

	/**
	 *
	 * @param array $definition
	 * @param IContextSource $context
	 * @param Config $config
	 */
	public function __construct( array $definition, IContextSource $context, Config $config ) {
		$this->initDataFromDefinition( $definition );
		parent::__construct( $context, $config );
	}

	/**
	 * returns the extension informations as an array
	 * @return array
	 */
	public function getInfo() {
		$info = [];
		foreach ( $this->allowedInfoKeys as $name => $defVal ) {
			$info[$name] = $this->$name;
		}
		return $info;
	}

	/**
	 * Returns the resource path for the current extension
	 * @return string
	 * @throws ConfigException
	 */
	public function getResourcePath() {
		return implode( '', [
			$this->getConfig()->get( 'ScriptPath' ),
			"/extensions",
			$this->getExtensionPath(),
			'/resources',
		] );
	}

	/**
	 * Returns the extensions Path. This is still a thing, when extensions
	 * are combined in packages
	 * @return string
	 */
	public function getExtensionPath() {
		return $this->get( 'extPath' );
	}

	/**
	 * Returns the name of the extension
	 * @return string
	 */
	public function getName() {
		return $this->get( 'name' );
	}

	/**
	 * Returns the authors names of the extension
	 * @return array
	 */
	public function getAuthors() {
		return $this->get( 'author' );
	}

	/**
	 * Returns the status of the extension. F.E.: stable, beta or alpha
	 * @return string
	 */
	public function getStatus() {
		return $this->get( 'status' );
	}

	/**
	 * Returns the package name of the extension. F.E.: BlueSpice Free or
	 * BlueSpice Pro
	 * @return string
	 */
	public function getPackage() {
		return $this->get( 'package' );
	}

	/**
	 * Returns the url of the Helpdesk page of the extension.
	 * @return string
	 */
	public function getUrl() {
		return $this->get( 'url' );
	}

	/**
	 * Returns the version of the extension. F.E.: 2.31.0
	 * @return string
	 */
	public function getVersion() {
		return $this->get( 'version' );
	}

	/**
	 * Returns the internal key of the extension. This should be removed in the
	 * future.
	 * @return string
	 */
	public function getExtensionKey() {
		return "MW::{$this->getName()}";
	}

	/**
	 * @param string $name Name of the info to retrieve
	 * @return mixed|null
	 */
	public function get( $name ) {
		if ( isset( $this->allowedInfoKeys[$name] ) ) {
			return $this->$name;
		}
		return null;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return bool
	 */
	public function set( $name, $value ) {
		if ( isset( $this->allowedInfoKeys[$name] ) ) {
			if ( $name === 'author' ) {
				return $this->setAuthors( $value );
			}
			$this->$name = $value;
			return true;
		}
		return false;
	}

	private function setAuthors( $value ) {
		$var = 'author';
		if ( !is_array( $value ) ) {
			$value = explode( ',', $value );
		}
		$value = array_map( static function ( $author ) {
			return trim( $author );
		}, $value );
		$this->$var = $value;
		return true;
	}

	private function initDataFromDefinition( $def ) {
		foreach ( $this->allowedInfoKeys as $name => $defVal ) {
			$value = $def;
			if ( isset( $def[$name] ) ) {
				$value = $def[$name];
			}
			$this->set( $name, $value );
		}
	}
}
