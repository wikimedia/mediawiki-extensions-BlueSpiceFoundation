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
	/**
	 * @deprecated since version 3.0.0 - use NotificationManager
	 * @param type $sHandler
	 */
	public static function registerNotificationHandler( $sHandler ) {
		//NO-OP
	}

	/**
	 * @see BSNotificationHandlerInterface::registerIcon
	 *
	 * @deprecated since version 3.0.0 - use NotificationManager
	 * @param String  $sKey
	 * @param String  $sLocation
	 * @param String  $sLocationType
	 * @param Boolean $bOverride
	 *
	 */
	public static function registerIcon(
		$sKey,
		$sLocation,
		$sLocationType = 'path',
		$bOverride = false
	) {
		if( $sLocationType != 'path' ) {
			//Only support path in new version
			return;
		}

		$notificationsManager = \BlueSpice\Services::getInstance()->getBSNotificationManager();

		$echoNotifier = $notificationsManager->getNotifier();

		if( $echoNotifier == null ) {
			return;
		}

		$echoNotifier->registerIcon(
			$sKey,
			[
				'path' => $sLocation
			]
		);
	}

	/**
	 * @see BSNotificationHandlerInterface::registerNotificationCategory
	 *
	 * @deprecated since version 3.0.0 - use NotificationManager
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
		$notificationsManager = \BlueSpice\Services::getInstance()->getBSNotificationManager();

		$echoNotifier = $notificationsManager->getNotifier();

		if( $echoNotifier == null ) {
			return;
		}

		$echoNotifier->registerNotificationCategory(
			$sKey,
			[
				'priority' => $iPriority,
				'usergroups' => $aUserGroups
			]
		);
	}

	/**
	 * @see BSNotificationHandlerInterface::registerNotification
	 *
	 * @deprecated since version 3.0.0 - use NotificationManager
	 * @throws BsException
	 */
	public static function registerNotification( /*...*/ ) {
		$aParams = func_get_args();

		if ( is_array ( $aParams[ 0 ] ) ) {
			$aValues = $aParams[ 0 ];
		} else {
			$aValues[ 'type' ] = $aParams[ 0 ];
			$aValues[ 'category' ] = $aParams[ 1 ];
			$aValues[ 'summary-message' ] = $aParams[ 2 ];
			$aValues[ 'summary-params' ] = $aParams[ 3 ];
			$aValues[ 'email-subject-message' ] = $aParams[ 4 ];
			$aValues[ 'email-subject-params' ] = $aParams[ 5 ];
			$aValues[ 'email-body-message' ] = $aParams[ 6 ];
			$aValues[ 'email-body-params' ] = $aParams[ 7 ];
			$aValues[ 'web-body-message' ] = $aParams[ 6 ];
			$aValues[ 'web-body-params' ] = $aParams[ 7 ];
			if ( isset ( $aParams[ 8 ] ) && is_array ( $aParams[ 8 ] ) ) {
				$aValues[ 'extra-params' ] = $aParams[ 8 ];
			} else {
				$aValues[ 'extra-params' ] = array ();
			}
		}

		$sType = $aValues['type'];
		unset( $aValues['type'] );

		$notificationsManager = \BlueSpice\Services::getInstance()->getBSNotificationManager();

		$echoNotifier = $notificationsManager->getNotifier();

		if( $echoNotifier == null ) {
			return;
		}

		$notificationsManager->registerNotification(
			$sType,
			$aValues
		);
	}

	/**
	 *
	 * @deprecated since version 3.0.0 - use NotificationManager
	 * @param $sKey
	 */
	public static function unregisterNotification(
		$sKey
	) {
		//NO-OP
	}

	/**
	 *
	 * @deprecated since version 3.0.0 - use NotificationManager
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
		$notificationsManager = \BlueSpice\Services::getInstance()->getBSNotificationManager();

		$echoNotifier = $notificationsManager->getNotifier();

		if( $echoNotifier == null ) {
			return;
		}

		$notification = $echoNotifier->getNotificationObject(
			$sKey,
			$oAgent,
			$oTitle,
			$aExtraParams
		);

		$echoNotifier->notify( $notification );
	}
}
