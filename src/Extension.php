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
 * For further information visit http://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice;

abstract class Extension extends Context implements \JsonSerializable {

	protected $extPath = '';

	protected $name = '';
	protected $authors = [];
	protected $version = '';
	protected $url = '';

	protected $status = '';
	protected $package = '';

	public function jsonSerialize() {
		return $this->getInfo();
	}

	public function __construct( array $definition, \IContextSource $context, \Config $config ) {
		$this->extPath = $definition['extPath'];
		$this->authors = $definition['author'];
		if( !is_array( $this->authors ) ) {
			$this->authors = explode( ',', $this->authors );
		}
		$this->package = $definition['package'];
		$this->status = $definition['status'];
		$this->name = $definition['name'];
		$this->url = $definition['url'];
		$this->version = $definition['version'];
		parent::__construct( $context, $config );
	}

	/**
	 * returns the extension informations as an array
	 * @return array
	 */
	public function getInfo() {
		return [
			'name' => $this->getName(),
			'authors' => $this->getAuthors(),
			'package' => $this->getPackage(),
			'status' => $this->getStatus(),
			'version' => $this->getVersion(),
			'url' => $this->getUrl(),
		];
	}

	/**
	 * Returns the resource path for the current extension
	 * @return string
	 */
	public function getResourcePath() {
		return implode( '', [
			$this->getConfig()->get( 'ScriptPath' ),
			"/extensions",
			$this->getExtensionPath(),
			'/resources',
		]);
	}

	/**
	 * Returns the extensions Path. This is still a thing, when extensions
	 * are combined in packages
	 * @return string
	 */
	public function getExtensionPath() {
		return $this->extPath;
	}

	/**
	 * Returns the name of the extension
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the authors names of the extension
	 * @return array
	 */
	public function getAuthors() {
		return $this->authors;
	}

	/**
	 * Returns the status of the extension. F.E.: stable, beta or alpha
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Returns the package name of the extension. F.E.: BlueSpice Free or
	 * BlueSpice Pro
	 * @return string
	 */
	public function getPackage() {
		return $this->package;
	}

	/**
	 * Returns the url of the Helpdesk page of the extension.
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Returns the version of the extension. F.E.: 2.31.0
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * Returns the internal key of the extension. This should be removed in the
	 * future.
	 * @return string
	 */
	public function getExtensionKey() {
		return "MW::{$this->getName()}";
	}
}
