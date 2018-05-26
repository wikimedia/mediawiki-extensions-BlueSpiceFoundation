<?php
/**
 * Default values for "BlueSpice MediaWiki" configuration settings.
 *
 *
 *                 NEVER EDIT THIS FILE
 *
 *
 * To customize your installation, edit "LocalSettings.php". If you make
 * changes here, they will be lost on next upgrade of BlueSpice MediaWiki!
 * *
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
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This file is part of MediaWiki and is not a valid entry point\n";
	die( 1 );
}

$GLOBALS['wgFooterIcons']['poweredby']['bluespice'] = array(
	"src" => $GLOBALS['wgScriptPath'] . "/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-poweredby_bluespice_88x31.png",
	"url" => "http://bluespice.com",
	"alt" => "Powered by BlueSpice",
);

/*
 * If this global is set to an array like
 * $bsgTestSystem = array(
 *	'color' => 'yellow', (or color code)
 *	'text' => 'Testsystem' (string that is shown in the colored box above the header)
 * );
 * the color will determine a div that is placed above the bs-wrapper
 * with the text element as a headline in it
 */
$GLOBALS[ 'bsgTestSystem' ] = false;

$GLOBALS[ 'bsgEnableRoleSystem' ] = false;

$GLOBALS[ 'bsgGroupRoles' ] = [
	'sysop' => ['admin' => true],
	'user' => ['editor' => true],
	'*' => ['reader' => true],
	'bot' => ['bot' => true],
	'bureaucrat' => ['editor' => true]
];

$GLOBALS[ 'bsgNamespaceRolesLockdown' ]  = [];

$GLOBALS[ 'bsgPermissionConfig' ] = [];

$GLOBALS[ 'bsgPermissionConfigDefault' ] = array(
	'read' => array(
		'type' => 'namespace',
		'preventLockout' => true,
		'roles' => [ 'reader', 'editor', 'admin' ]
	),
	'edit' => array(
		'type' => 'namespace',
		'preventLockout' => true,
		'roles' => [ 'editor', 'admin' ]
	),
	'delete' => array(
		'type' => 'namespace',
		'preventLockout' => true,
		'roles' => [ 'editor', 'admin' ]
	),
	'siteadmin' => array(
		'type' => 'global',
		'preventLockout' => true,
		'roles' => [ 'admin' ]
	),
	'wikiadmin' => array(
		'type' => 'global',
		'preventLockout' => true,
		'roles' => [ 'admin' ]
	),
	"apihighlimits" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"autoconfirmed" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"autopatrol" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"bigdelete" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"block" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"blockemail" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"bot" => array(
		'type' => 'global',
		'roles' => ['bot']
	),
	"browsearchive" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"createaccount" => array(
		'type' => 'global',
		'roles' => [ 'editor', 'admin' ]
	),
	"createpage" => array(
		'type' => 'namespace',
		'roles' => [ 'admin', 'editor' ]
	),
	"move" => array(
		'type' => 'namespace',
		'roles' => [ 'admin', 'editor' ]
	),
	"createtalk" => array(
		'type' => 'namespace',
		'roles' => [ 'admin', 'editor' ]
	),
	"create" => array(
		'type' => 'namespace',
		'roles' => [ 'admin', 'editor' ]
	),
	"purge" => array(
		'type' => 'namespace',
		'roles' => [ 'admin', 'editor', 'reader' ]
	),
	"editinterface" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"editmywatchlist" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"viewmywatchlist" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"editmyprivateinfo" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"viewmyprivateinfo" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"editmyoptions" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"editusercss" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"editmyusercss" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"edituserjs" => array(
		'type' => 'global',
		'roles' => [ 'editor' ],
		'roles' => [ 'admin', 'editor' ]
	),
	"editmyuserjs" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"hideuser" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"import" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"importupload" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"upload" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"minoredit" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"ipblock-exempt" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"move-rootuserpages" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"override-export-depth" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"passwordreset" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"sendemail" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor' ]
	),
	"unblockself" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"userrights" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"userrights-interwiki" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"writeapi" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'reader', 'editor' ]
	),
	"skipcaptcha" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"renameuser" => array(
		'type' => 'global',
		'roles' => [ 'admin' ]
	),
	"viewfiles" => array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor', 'reader' ]
	),
	"searchfiles"=> array(
		'type' => 'global',
		'roles' => [ 'admin', 'editor', 'reader' ]
	)
);

/**
 * Allows extensions to distinguish between normal content NS, that can be
 * renamed of deleted and system NS that can not be modified. Used in
 * BlueSpiceExtensions/NamespaceManager and NamespaceHelper
 */
$GLOBALS['bsgSystemNamespaces'] = array(
	//1599 => 'NS_COOL_STUFF'
);

$GLOBALS['wgResourceLoaderLESSVars'] = array_merge( $GLOBALS['wgResourceLoaderLESSVars'], array(
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
	'bs-color-destructive' => '#d11d13',

	//Message boxes
	'bs-color-success' => '#dff0d8',
	'bs-color-warning' => '#fcf8e3',
	'bs-color-error' => '#f2dede',
	'bs-color-info' => '#d9edf7'
) );
/**
 * BsExtensionManager extension registration
 */
$GLOBALS['bsgExtensions'] = [];

/**
 * BsTemplateHelper template directory overwrite
 * $bsgTemplates = array(
 *    BSExtension.Template.Name": "$wgExtensionsDirectory/MyExtension/PathToTemplateDir",
 * )
 */
$GLOBALS['bsgTemplates'] = array();


/*
 * ExtJSThemes
 */
$GLOBALS["bsgExtJSThemes"] = array(
	"white" => array(
		'bs-extjs-theme-primary-text-color' => 'black',
		'bs-extjs-theme-secondary-text-color' => 'black',
		'bs-extjs-theme-primary-background-color' => 'white',
		'bs-extjs-theme-secondary-background-color' => 'white',
		'bs-extjs-theme-toolbar-footer-background' => 'white',
		'bs-extjs-theme-btn-small-border' => '1px solid @bs-color-neutral4',
		'bs-extjs-theme-tab-active-background' => '@bs-color-neutral4',
		'bs-extjs-theme-tab-active-text' => 'white',
		'bs-extjs-theme-tab-active-strip' => '@bs-color-neutral4',
		'bs-extjs-theme-panel-border' => '5px solid @bs-color-neutral4',
		'bs-extjs-theme-panel-header-horizontal-border' => '1px solid @bs-color-neutral4',
		'bs-extjs-theme-panel-header-vertical-border' => '1px solid @bs-color-neutral4',
		'bs-extjs-theme-btn-toolbar-noicon-border' => '1px solid black',
		'bs-extjs-theme-tool-img-image' => 'url( "/extensions/BlueSpiceFoundation/resources/bluespice.extjs/bluespice-theme/images/tools/tool-sprites-dark.png" )',
		'bs-extjs-theme-btn-split-right-image' => 'url( "/extensions/BlueSpiceFoundation/resources/bluespice.extjs/bluespice-theme/images/button/default-toolbar-small-s-arrow.png" )',
	)
);

$GLOBALS['bsgUserMiniProfileParams'] = [ 'width' => 40, 'height' => 40 ];
$GLOBALS['bsgMiniProfileEnforceHeight'] = true;
$GLOBALS['bsgPingInterval'] = 10;

/**
 * ATTENTION: Not to confuse with 'bsgTestSystem'.
 * This settings influences e-mail-, export- and other features
 * @global string $GLOBALS['bsgTestMode']
 * @name $bsgTestMode
 */
$GLOBALS['bsgTestMode'] = false;
$GLOBALS['bsgFileExtensions'] = [
			'txt', 'rtf',
			'doc', 'dot', 'docx', 'dotx', 'dotm',
			'xls', 'xlt', 'xlm', 'xlsx', 'xlsm', 'xltm', 'xltx',
			'ppt', 'pot', 'pps', 'pptx', 'pptm', 'potx', 'potm', 'ppsx', 'ppsm', 'sldx', 'sldm',
			'odt', 'fodt', 'ods', 'fods', 'odp', 'fodp',
			'pdf',
			'zip', 'rar', 'tar', 'tgz', 'gz', 'bzip2', '7zip',
			'xml', 'svg'
];


$GLOBALS['bsgImageExtensions'] = [ 'png', 'gif', 'jpg', 'jpeg' ];
