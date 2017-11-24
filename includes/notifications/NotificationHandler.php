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
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
interface BSNotificationHandlerInterface {
	public static function init();

	/**
	 * Registers a new icon or override the registration of an existing icon at the notification handler.
	 *
	 * @param String  $sKey          A string which identifies the registered icon.
	 * @param String  $sLocation     The location string of the registered icon. Depending on what is the location
	 *                               type, this can be either a path, relative to the assets directory, or an absolute
	 *                               url.
	 * @param String  $sLocationType The location type which can be either 'path' or 'url'. (Default: 'path')
	 * @param Boolean $bOverride     A flag to determine if one wants to override an already registered icon. (Default:
	 *                               false)
	 */
	public static function registerIcon(
		$sKey,
		$sLocation,
		$sLocationType = 'path',
		$bOverride = false
	);

	/**
	 * Registers a new notification category at the notification handler.
	 *
	 * @param String  $sKey                      A string which identifies the registered category.
	 * @param Integer $iPriority                 Sets the priority of this category. In most cases, this value doesn't
	 *                                           need to be set.
	 *                                           (Default: 10)
	 * @param Array   $aNoDismiss                If set, this specifies that a notification cannot be turned off with
	 *                                           the user's notification preferences. The value is an array containing
	 *                                           the value 'all' or all the output formats which should not be turned
	 *                                           off. E.g. <code>array('all')</code> or
	 *                                           <code>array('web')</code>
	 * @param String  $sTooltipMsgKey            The message key for the messages shown as a explanation of this
	 *                                           category on the notification preferences page.
	 * @param Array   $aUserGroups               An array containing the user groups which are eligible to receive the
	 *                                           notifications of this category. If not set, all groups are eligible.
	 * @param Array   $aActiveDefaultUserOptions An array containing the output formats which should be turned on by
	 *                                           default in the user's notification preferences. E.g.
	 *                                           <code>array('web')</code>
	 */
	public static function registerNotificationCategory(
		$sKey,
		$iPriority = 10,
		$aNoDismiss = null,
		$sTooltipMsgKey = null,
		$aUserGroups = null,
		$aActiveDefaultUserOptions = null
	);

	/**
	 * Registers a new notification message at the notification handler.
	 *
	 * @param String $sKey                A string which identifies the registered message.
	 * @param String $sCategory           A string which specifies the notification category this message belongs to.
	 *                                    (@see BSNotificationHandlerInterface::registerNotificationCategory)
	 * @param String $sSummaryMsgKey      A message key for the message shown in the notification flyout.
	 * @param Array  $aSummaryParams      An array containing all the parameter keys used in the summary message. The
	 *                                    order of the key defines the parameter index in the message.
	 * @param String $sEmailSubjectMsgKey A message key for the message used as the email subject for the notification
	 *                                    emails.
	 * @param Array  $aEmailSubjectParams An array containing all the parameter keys used in the subject message. The
	 *                                    order of the key defines the parameter index in the message.
	 * @param String $sEmailBodyMsgKey    A message key for the message used as the email body for the notification
	 *                                    emails.
	 * @param Array  $aEmailBodyParams    An array containing all the parameter keys used in the body message. The
	 *                                    order
	 *                                    of the key defines the parameter index in the message.
	 * @param Array  $aExtraParams        An array which contains optional settings for this message. For an
	 *                                    explanation
	 *                                    see
	 *                                    https://www.mediawiki.org/wiki/Echo_(Notifications)/Developer_guide#Notification_parameters
	 */
	public static function registerNotification( $aParams );

	/**
	 * Removes a notification message from the notification handler.
	 *
	 * @param String $sKey A string which identifies the message to remove.
	 */
	public static function unregisterNotification( $sKey );

	/**
	 * Sends a registered notification message.
	 *
	 * @param String $sKey         A string which identifies the notification message to be sent.
	 * @param User   $oAgent       A User instance for the user which causes this notification.
	 * @param Title  $oTitle       A Title instance for the page, this notification is about.
	 * @param Array  $aExtraParams An array which contains any additional parameters for the notification formatter.
	 *                             see
	 *                             https://www.mediawiki.org/wiki/Echo_(Notifications)/Developer_guide#Creating_a_new_formatter_class
	 */
	public static function notify(
		$sKey,
		$oAgent = null,
		$oTitle = null,
		$aExtraParams = null
	);
}

abstract class BSNotificationHandler implements BSNotificationHandlerInterface {
	public static function init() {
	}

	public static function registerIcon(
		$sKey,
		$sLocation,
		$sLocationType = 'path',
		$bOverride = false
	) {
	}

	public static function registerNotificationCategory(
		$sKey,
		$iPriority = 10,
		$aNoDismiss = null,
		$sTooltipMsgKey = null,
		$aUserGroups = null,
		$aActiveDefaultUserOptions = null
	) {
	}

	public static function registerNotification( $aParams ) {
	}

	public static function unregisterNotification( $sKey ) {
	}

	public static function notify(
		$sKey,
		$oAgent = null,
		$oTitle = null,
		$aExtraParams = null
	) {
	}
}