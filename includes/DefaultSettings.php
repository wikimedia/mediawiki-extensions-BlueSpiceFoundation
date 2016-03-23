<?php
/**
 * Default values for "BlueSpice for MediaWiki" configuration settings.
 *
 *
 *                 NEVER EDIT THIS FILE
 *
 *
 * To customize your installation, edit "LocalSettings.php". If you make
 * changes here, they will be lost on next upgrade of BlueSpice for MediaWiki!
 * *
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
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This file is part of MediaWiki and is not a valid entry point\n";
	die( 1 );
}

//Default settings needed for BlueSpice
$wgNamespacesWithSubpages[NS_MAIN] = true;
$wgApiFrameOptions = 'SAMEORIGIN';
$wgRSSUrlWhitelist = array(
	"http://blog.blue-spice.org/feed/",
	"http://blog.hallowelt.com/feed/",
);
$wgExternalLinkTarget = '_blank';
$wgCapitalLinkOverrides[ NS_FILE ] = false;
$wgRestrictDisplayTitle = false; //Otherwise only titles that normalize to the same DB key are allowed
$wgUrlProtocols[] = "file://";
$wgVerifyMimeType = false;
$wgAllowJavaUploads = true;
$wgThumbnailScriptPath = "{$wgScriptPath}/thumb{$wgScriptExtension}"; //Enable on demand thumb rendering

$bsgPermissionConfig = array(
	'read' => array(
		'type' => 'namespace',
		'preventLockout' => true
	),
	'siteadmin' => array(
		'type' => 'global',
		'preventLockout' => true
	),
	'wikiadmin' => array(
		'type' => 'global',
		'preventLockout' => true
	),
	"apihighlimits" => array(
		'type' => 'global'
	),
	"autoconfirmed" => array(
		'type' => 'global'
	),
	"autopatrol" => array(
		'type' => 'global'
	),
	"bigdelete" => array(
		'type' => 'global'
	),
	"block" => array(
		'type' => 'global'
	),
	"blockemail" => array(
		'type' => 'global'
	),
	"bot" => array(
		'type' => 'global'
	),
	"browsearchive" => array(
		'type' => 'global'
	),
	"createaccount" => array(
		'type' => 'global'
	),
	"editinterface" => array(
		'type' => 'global'
	),
	"editmywatchlist" => array(
		'type' => 'global'
	),
	"viewmywatchlist" => array(
		'type' => 'global'
	),
	"editmyprivateinfo" => array(
		'type' => 'global'
	),
	"viewmyprivateinfo" => array(
		'type' => 'global'
	),
	"editmyoptions" => array(
		'type' => 'global'
	),
	"editusercssjs" => array(
		'type' => 'global'
	),
	"editusercss" => array(
		'type' => 'global'
	),
	"editmyusercss" => array(
		'type' => 'global'
	),
	"edituserjs" => array(
		'type' => 'global'
	),
	"editmyuserjs" => array(
		'type' => 'global'
	),
	"hideuser" => array(
		'type' => 'global'
	),
	"import" => array(
		'type' => 'global'
	),
	"importupload" => array(
		'type' => 'global'
	),
	"ipblock-exempt" => array(
		'type' => 'global'
	),
	"move-rootuserpages" => array(
		'type' => 'global'
	),
	"override-export-depth" => array(
		'type' => 'global'
	),
	"passwordreset" => array(
		'type' => 'global'
	),
	"proxyunbannable" => array(
		'type' => 'global'
	),
	"sendemail" => array(
		'type' => 'global'
	),
	"unblockself" => array(
		'type' => 'global'
	),
	"userrights" => array(
		'type' => 'global'
	),
	"userrights-interwiki" => array(
		'type' => 'global'
	),
	"writeapi" => array(
		'type' => 'global'
	),
	"skipcaptcha" => array(
		'type' => 'global'
	),
	"renameuser" => array(
		'type' => 'global'
	),
	"viewfiles" => array(
		'type' => 'global'
	),
	"searchfiles"=> array(
		'type' => 'global'
	)
);

/**
 * Allows extensions to distinguish between normal content NS, that can be
 * renamed of deleted and system NS that can not be modified. Used in
 * BlueSpiceExtensions/NamespaceManager and NamespaceHelper
 */
$bsgSystemNamespaces = array(
	//1599 => 'NS_COOL_STUFF'
);

/**
 * PHP config files registered here will be included on "SetupAfterCache"
 * time. Access to all global config variables need to be in the form of
 * $GLOBALS['wg...'] as the inclusion will be done in callback function scope
 * rather than in global scope.
 */
$bsgConfigFiles = array(
	//'extensionname' => 'path/to/file.php'

	//Pre-registering for BC; Should be removed in future releases
	'GroupManager' => BSCONFIGDIR . DS . 'gm-settings.php',
	'NamespaceManager' => BSCONFIGDIR . DS . 'nm-settings.php',
	'PermissionManager' => BSCONFIGDIR . DS . 'pm-settings.php',
);


$wgResourceLoaderLESSVars += array(
	'bs-color-primary' => '#3e5389', //blue
	'bs-color-secondary' => '#ffae00', //orange
	'bs-color-tertiary' => '#b73a3a', //red
	'bs-color-neutral' => '#929292', //grey
	'bs-color-neutral2' => '#ABABAB', //lighten(@bs-color-neutral1, 10%); - LESS / RL issue
	'bs-color-neutral3' => '#C4C4C4', //lighten(@bs-color-neutral1, 20%)',
	'bs-color-neutral4' => '#787878', //darken(@bs-color-neutral1, 10%)'

	//From http://tools.wmflabs.org/styleguide/desktop/section-2.html
	'bs-color-progressive' => '#347bff',
	'bs-color-contructive' => '#00af89',
	'bs-color-destructive' => '#d11d13'
);