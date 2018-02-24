<?php
/**
 * Hook handler base class for BlueSpice hook BSCoreGetUserMiniProfileBeforeInit
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
namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class BSCoreGetUserMiniProfileBeforeInit extends Hook {

	/**
	 *
	 * @var \ViewUserMiniProfile
	 */
	protected $userMiniProfileView = null;

	/**
	 *
	 * @var \User
	 */
	protected $user = null;

	/**
	 *
	 * @var array
	 */
	protected $params = null;

	/**
	 * Located in \BsCore::getUserMiniProfile. Before the created user mini
	 * profile view is initialized and returned.
	 * @param \ViewUserMiniProfile $userMiniProfileView
	 * @param \User $user
	 * @param array $params
	 * @return boolean
	 */
	public static function callback( &$userMiniProfileView, &$user, &$params ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$userMiniProfileView,
			$user,
			$params
		);
		return $hookHandler->process();
	}

	/**
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \ViewUserMiniProfile $userMiniProfileView
	 * @param \User $user
	 * @param array $params
	 */
	public function __construct( $context, $config, &$userMiniProfileView, &$user, &$params ) {
		parent::__construct( $context, $config );

		$this->userMiniProfileView = &$userMiniProfileView;
		$this->user = &$user;
		$this->params = &$params;
	}
}
