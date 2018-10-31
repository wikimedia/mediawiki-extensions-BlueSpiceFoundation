<?php

/**
 * UtilityFactory class for BlueSpice
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
 * @copyright  Copyright (C) 2018 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice;
use BlueSpice\Services;

/**
 * UtilityFactory class for BlueSpice
 * @package BlueSpiceFoundation
 */
class UtilityFactory {

	/**
	 *
	 * @var Services
	 */
	protected $services = null;

	/**
	 * @param Services $services
	 */
	public function __construct( Services $services ) {
		$this->services = $services;
	}

	/**
	 *
	 * @param array $params
	 * @params \Title|null $default
	 * @return \BlueSpice\Utility\TitleParamsResolver
	 */
	public function getTitleParamsResolver( $params, $default = null ) {
		return new \BlueSpice\Utility\TitleParamsResolver( $params, $default );
	}

	/**
	 * @param string $url
	 * @return \BlueSpice\Utility\UrlTitleParser
	 */
	public function getUrlTitleParser( $url ) {
		return new \BlueSpice\Utility\UrlTitleParser(
			$url,
			$this->services->getConfigFactory()->makeConfig( 'bsg' )
		);
	}

	/**
	 * @param \User|null $user
	 * @return \BlueSpice\Utility\UserHelper
	 */
	public function getUserHelper( $user = null ) {
		return new \BlueSpice\Utility\UserHelper( $user );
	}

	/**
	 *
	 * @return \BlueSpice\Utility\MaintenanceUser
	 */
	public function getMaintenanceUser() {
		return new \BlueSpice\Utility\MaintenanceUser(
			$this->services->getConfigFactory()->makeConfig( 'bsg' )
		);
	}

	/**
	 * @param string $wikitext
	 * @return \BlueSpice\Utility\WikiTextLinksHelper
	 */
	public function getWikiTextLinksHelper( $wikitext ) {
		return new \BlueSpice\Utility\WikiTextLinksHelper(
			$wikitext
		);
	}

	/**
	 * @return \BlueSpice\Utility\CacheHelper
	 */
	public function getCacheHelper() {
		return new \BlueSpice\Utility\CacheHelper(
			$this->services->getConfigFactory()->makeConfig( 'bsg' )
		);
	}
}
