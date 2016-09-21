<?php

/**
 * BSEntityRegistry class for BlueSpice
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
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
 * This file is part of BlueSpice for MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * BSEntityRegistry class for BSSocial extension
 * @package BlueSpiceFoundation
 */
class BSEntityRegistry {
	private function __construct() {}
	private static $bEntitiesRegistered = false;
	private static $aEntities = array();

	protected static function runRegister( $bForceReload = false ) {
		if( static::$bEntitiesRegistered && !$bForceReload ) {
			return true;
		}

		$b = wfRunHooks( 'BSEntityRegister', array(
			&self::$aEntities,
			//&self::$sDefaultHandlerType,
		));

		return $b ? static::$bEntitiesRegistered = true : $b;
	}

	/**
	 * Returns all registered entities ( type => EntityConfigClass )
	 * @return array
	 */
	public static function getRegisteredEntities() {
		if( !self::runRegister() ) {
			return array();
		}
		return self::$aEntities;
	}

	/**
	 * Checks if given type is a registered Entity
	 * @param string $sType
	 * @return bool
	 */
	public static function isRegisteredType( $sType ) {
		return in_array(
			$sType,
			self::getRegisterdTypeKeys()
		);
	}

	/**
	 * Returns a registered entity by given type
	 * @param string $sType
	 * @return array
	 */
	public static function getRegisteredEntityByType( $sType ) {
		if( !self::isRegisteredType($sType) ) {
			return array();
		}
		return self::$aEntities[$sType];
	}

	/**
	 * Returns all registered entity types
	 * @return array
	 */
	public static function getRegisterdTypeKeys() {
		return array_keys(
			self::getRegisteredEntities()
		);
	}
}