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
 * For further information visit https://bluespice.com
 *
 * @author     Patric Wirth
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2018 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice;

use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

/**
 * UtilityFactory class for BlueSpice
 * @package BlueSpiceFoundation
 */
class UtilityFactory {

	/**
	 *
	 * @var MediaWikiServices
	 */
	protected $services = null;

	/**
	 * @param MediaWikiServices $services
	 */
	public function __construct( MediaWikiServices $services ) {
		$this->services = $services;
	}

	/**
	 *
	 * @param array $params
	 * @param Title[] $default
	 * @return \BlueSpice\Utility\TitleParamsResolver
	 */
	public function getTitleParamsResolver( $params, $default = [] ) {
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
	 * @param User|null $user
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

	/**
	 * @param Title $title
	 * @return \BlueSpice\Utility\PagePropHelper
	 * @deprecated since 4.3 Use MediaWiki core `\PageProps` instead
	 */
	public function getPagePropHelper( Title $title ) {
		wfDeprecated( __METHOD__, '4.3' );
		return new \BlueSpice\Utility\PagePropHelper( $this->services, $title );
	}

	/**
	 * @return \BlueSpice\Utility\TemplateHelper
	 */
	public function getTemplateHelper() {
		return new \BlueSpice\Utility\TemplateHelper( $this->services );
	}

	/**
	 * @return \BlueSpice\Utility\GroupHelper
	 */
	public function getGroupHelper() {
		$groupManager = $this->services->getUserGroupManager();
		$config = $this->services->getMainConfig();
		$additionalGroups = $config->get( 'AdditionalGroups' );
		$groupTypes = $config->get( 'GroupTypes' );
		$dbr = $this->services->getDBLoadBalancer()->getConnection( DB_REPLICA );

		return new \BlueSpice\Utility\GroupHelper(
			$groupManager, $additionalGroups, $groupTypes, $dbr, $this->services->getUserFactory()
		);
	}
}
