<?php
/**
 * Hook handler base class for BlueSpice hook BS:UserPageSettings
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

abstract class BSUserPageSettings extends Hook {
	/**
	 * The user related to the user page
	 * @var \User
	 */
	protected $user = null;

	/**
	 * The user page
	 * @var \Title
	 */
	protected $title = null;

	/**
	 * An array of \ViewBaseElement's
	 * @var []\ViewBaseElement
	 */
	protected $settingViews = null;

	/**
	 * This hook is called: 'BS:UserPageSettings'
	 * Located in BsCoreHooks::addProfilePageSettings. This is where the
	 * user setting buttons on the users page are registered, when the current
	 * user is on his user page.
	 * @param \User $user
	 * @param \Title $title
	 * @param array $settingViews
	 * @return boolean
	 */
	public static function callback( $user, $title, &$settingViews ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$user,
			$title,
			$settingViews
		);
		return $hookHandler->process();
	}

	/**
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \User $user
	 * @param \Title $title
	 * @param array $settingViews
	 * @return boolean
	 */
	public function __construct( $context, $config, $user, $title, &$settingViews ) {
		parent::__construct( $context, $config );

		$this->user = $user;
		$this->title = $title;
		$this->settingViews = &$settingViews;
	}
}