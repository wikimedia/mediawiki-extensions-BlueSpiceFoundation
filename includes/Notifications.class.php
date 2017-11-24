<?php

/**
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
 * @author     Sebastian Ulbricht <o0lilu0o1980@gmail.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
abstract class BSNotifications {
	private static $aNotificationHandlers = array();

	public static function registerNotificationHandler( $sHandler ) {
		self::$aNotificationHandlers[] = $sHandler;
	}

	private static function getNotificationHandlerMethods( $sMethod ) {
		$aNotificationHandlers = self::$aNotificationHandlers;
		if ( empty( $aNotificationHandlers ) ) {
			return array();
		}

		$aCallableMethods = array();
		foreach ( $aNotificationHandlers as $sHandler ) {
			$sCallable = "$sHandler::$sMethod";
			if ( !is_callable( $sCallable ) ) {
				throw new BsException(
					"Notification Handler $sHandler does not have a method $sMethod!"
				);
			}
			$aCallableMethods[ $sHandler ] = $sMethod;
		}

		return $aCallableMethods;
	}

	public static function init() {
		Hooks::run('BeforeNotificationsInit');

		$aNotificationHandlerMethods = self::getNotificationHandlerMethods(
			__FUNCTION__
		);

		foreach ( $aNotificationHandlerMethods as $sHandler => $sCallable ) {
			$sHandler::$sCallable();
		}
	}

	/**
	 * @see BSNotificationHandlerInterface::registerIcon
	 *
	 * @param String  $sKey
	 * @param String  $sLocation
	 * @param String  $sLocationType
	 * @param Boolean $bOverride
	 *
	 * @throws BsException
	 */
	public static function registerIcon(
		$sKey,
		$sLocation,
		$sLocationType = 'path',
		$bOverride = false
	) {
		$aNotificationHandlerMethods = self::getNotificationHandlerMethods(
			__FUNCTION__
		);

		foreach ( $aNotificationHandlerMethods as $sHandler => $sCallable ) {
			$sHandler::$sCallable(
				$sKey,
				$sLocation,
				$sLocationType,
				$bOverride
			);
		}
	}

	/**
	 * @see BSNotificationHandlerInterface::registerNotificationCategory
	 *
	 * @param String  $sKey
	 * @param Integer $iPriority
	 * @param Array   $aNoDismiss
	 * @param String  $sTooltipMsgKey
	 * @param Array   $aUserGroups
	 * @param Array   $aActiveDefaultUserOptions
	 *
	 * @throws BsException
	 */
	public static function registerNotificationCategory(
		$sKey,
		$iPriority = 10,
		$aNoDismiss = null,
		$sTooltipMsgKey = null,
		$aUserGroups = null,
		$aActiveDefaultUserOptions = null
	) {
		$aNotificationHandlerMethods = self::getNotificationHandlerMethods(
			__FUNCTION__
		);

		foreach ( $aNotificationHandlerMethods as $sHandler => $sCallable ) {
			$sHandler::$sCallable(
				$sKey,
				$iPriority,
				$aNoDismiss,
				$sTooltipMsgKey,
				$aUserGroups,
				$aActiveDefaultUserOptions
			);
		}
	}

	/**
	 * @see BSNotificationHandlerInterface::registerNotification
	 *
	 *
	 * @throws BsException
	 */
	public static function registerNotification( /*...*/ ) {
		$aParams = func_get_args();

		$aNotificationHandlerMethods = self::getNotificationHandlerMethods(
			__FUNCTION__
		);

		foreach ( $aNotificationHandlerMethods as $sHandler => $sCallable ) {
			$sHandler::$sCallable( $aParams );
		}
	}

	/**
	 * @see BSNotificationHandlerInterface::unregisterNotification
	 *
	 * @param $sKey
	 *
	 * @throws BsException
	 */
	public static function unregisterNotification(
		$sKey
	) {
		$aNotificationHandlerMethods = self::getNotificationHandlerMethods(
			__FUNCTION__
		);

		foreach ( $aNotificationHandlerMethods as $sHandler => $sCallable ) {
			$sHandler::$sCallable(
				$sKey
			);
		}
	}

	/**
	 * @see BSNotificationHandlerInterface::notify
	 *
	 * @param String $sKey
	 * @param User   $oAgent
	 * @param Title  $oTitle
	 * @param Array  $aExtraParams
	 *
	 * @throws BsException
	 */
	public static function notify(
		$sKey,
		$oAgent = null,
		$oTitle = null,
		$aExtraParams = null
	) {
		$aNotificationHandlerMethods = self::getNotificationHandlerMethods(
			__FUNCTION__
		);

		foreach ( $aNotificationHandlerMethods as $sHandler => $sCallable ) {
			$sHandler::$sCallable(
				$sKey,
				$oAgent,
				$oTitle,
				$aExtraParams
			);
		}
	}
}