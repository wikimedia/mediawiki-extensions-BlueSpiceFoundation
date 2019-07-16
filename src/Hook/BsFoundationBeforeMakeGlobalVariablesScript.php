<?php
/**
 * DEPRECATED
 * Hook handler base class for BlueSpice hook
 * BsFoundationBeforeMakeGlobalVariablesScript
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
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;

abstract class BsFoundationBeforeMakeGlobalVariablesScript extends Hook {

	/**
	 *
	 * @var \User
	 */
	protected $user = null;

	/**
	 *
	 * @var array
	 */
	protected $scriptSettings = null;

	/**
	 * DEPRECATED!
	 * Located in
	 * \BlueSpice\Hook\BeforePageDisplay\AddResources::addLegacyJSConfigVars,
	 * before the javascript globals for BlueSpice get added to the javascript
	 * globals
	 * @param \User $user
	 * @param array $scriptSettings
	 * @return boolean
	 */
	public static function callback( $user, &$scriptSettings ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$user,
			$scriptSettings
		);
		return $hookHandler->process();
	}

	/**
	 * @param \IContextSource $context
	 * @param \Config $config
	 */
	public function __construct( $context, $config, $user, &$scriptSettings ) {
		parent::__construct( $context, $config );

		$this->user = $user;
		$this->scriptSettings = &$scriptSettings;
	}
}
