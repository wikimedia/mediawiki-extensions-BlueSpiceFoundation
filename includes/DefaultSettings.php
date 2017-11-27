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
$GLOBALS['bsgTestSystem'] = false;

$GLOBALS['bsgPermissionConfig'] = array(
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
	'bs-color-info' => '#d9edf7',

	//extjs theme

	//base colors
	'bs-extjs-theme-primary-text-color' => 'white',
	'bs-extjs-theme-secondary-text-color' => 'black',
	'bs-extjs-theme-primary-background-color' => '@bs-color-primary',
	'bs-extjs-theme-primary-background-color-1st-derivate' => 'lighten( desaturate( @bs-extjs-theme-primary-background-color, abs(11%) ), 9% )',
	'bs-extjs-theme-primary-background-color-2nd-derivate' => 'lighten( desaturate( spin( @bs-extjs-theme-primary-background-color, 2), abs(20%) ) , 51.5% )',
	'bs-extjs-theme-primary-background-color-3rd-derivate' => 'lighten( desaturate( spin( @bs-extjs-theme-primary-background-color, 1), abs(10%) ) , 40% )',
	'bs-extjs-theme-secondary-background-color' => 'lighten( @bs-color-neutral4, 49% )',

	//images (i. e. close, toggle)
	'bs-extjs-theme-tool-img-image' => 'url( "bluespice-theme/images/tools/tool-sprites.png" )',
	'bs-extjs-theme-tool-img-background' => 'transparent',
	'bs-extjs-theme-tool-toggle-img-image' => 'url( "bluespice-theme/images/fieldset/collapse-tool.png" )',
	'bs-extjs-theme-tool-toggle-img-background' => 'transparent',
	'bs-extjs-theme-btn-split-right-image' => 'url( "bluespice-theme/images/button/default-small-s-arrow.png" )',

	//Buttons ( both, OK and Cancel )
	'bs-extjs-theme-btn-small-text' => '@bs-extjs-theme-primary-text-color',
	'bs-extjs-theme-btn-small-border' => '1px solid @bs-extjs-theme-btn-small-background',
	'bs-extjs-theme-btn-small-background' => '@bs-extjs-theme-primary-background-color-1st-derivate',
	'bs-extjs-theme-btn-small-background-0' => 'lighten( saturate( @bs-extjs-theme-btn-small-background, abs(2%) ), 5% )',
	'bs-extjs-theme-btn-small-background-50' => '@bs-extjs-theme-btn-small-background',
	'bs-extjs-theme-btn-small-background-51' => 'darken( @bs-extjs-theme-btn-small-background, 2.5% )',
	'bs-extjs-theme-btn-small-background-100' => '@bs-extjs-theme-btn-small-background',

	//Button OK ( explicit )
	'bs-extjs-theme-btn-small-ok-text' => '@bs-extjs-theme-btn-small-text',
	'bs-extjs-theme-btn-small-ok-border' => '@bs-extjs-theme-btn-small-border',
	'bs-extjs-theme-btn-small-ok-background' => '@bs-extjs-theme-btn-small-background',
	'bs-extjs-theme-btn-small-ok-background-0' => 'lighten( saturate( @bs-extjs-theme-btn-small-ok-background, abs(2%) ), 5% )',
	'bs-extjs-theme-btn-small-ok-background-50' => '@bs-extjs-theme-btn-small-ok-background',
	'bs-extjs-theme-btn-small-ok-background-51' => 'darken( @bs-extjs-theme-btn-small-ok-background, 2.5% )',
	'bs-extjs-theme-btn-small-ok-background-100' => '@bs-extjs-theme-btn-small-ok-background',

	//Button Cancel ( explicit )
	'bs-extjs-theme-btn-small-cancel-text' => '@bs-extjs-theme-btn-small-text',
	'bs-extjs-theme-btn-small-cancel-border' => '@bs-extjs-theme-btn-small-border',
	'bs-extjs-theme-btn-small-cancel-background' => '@bs-extjs-theme-btn-small-background',
	'bs-extjs-theme-btn-small-cancel-background-0' => 'lighten( saturate( @bs-extjs-theme-btn-small-cancel-background, abs(2%) ), 5% )',
	'bs-extjs-theme-btn-small-cancel-background-50' => '@bs-extjs-theme-btn-small-cancel-background',
	'bs-extjs-theme-btn-small-cancel-background-51' => 'darken( @bs-extjs-theme-btn-small-cancel-background, 2.5% )',
	'bs-extjs-theme-btn-small-cancel-background-100' => '@bs-extjs-theme-btn-small-cancel-background',

	//Button i. e. Insert image -> image size
	'bs-extjs-theme-btn-toolbar-small-text' => '@bs-extjs-theme-secondary-text-color',
	'bs-extjs-theme-btn-toolbar-small-border' => '1px solid darken( @bs-extjs-theme-btn-toolbar-small-background, 8% )',
	'bs-extjs-theme-btn-toolbar-small-background' => '@bs-extjs-theme-secondary-background-color',
	'bs-extjs-theme-btn-toolbar-small-background-0' => 'lighten( @bs-extjs-theme-btn-toolbar-small-background, 0.5% )',
	'bs-extjs-theme-btn-toolbar-small-background-50' => '@bs-extjs-theme-btn-toolbar-small-background',
	'bs-extjs-theme-btn-toolbar-small-background-51' => 'darken( @bs-extjs-theme-btn-toolbar-small-background, 5% )',
	'bs-extjs-theme-btn-toolbar-small-background-100' => '@bs-extjs-theme-btn-toolbar-small-background',

	//Button in toolbar with icon (Bookmaker, Flexiskin)
	'bs-extjs-theme-btn-toolbar-text' => '@bs-extjs-theme-secondary-text-color',
	'bs-extjs-theme-btn-toolbar-border' => '1px solid @bs-extjs-theme-btn-toolbar-background',
	'bs-extjs-theme-btn-toolbar-background' => '@bs-extjs-theme-secondary-background-color',
	'bs-extjs-theme-btn-toolbar-background-0' => 'lighten( saturate( @bs-extjs-theme-btn-toolbar-background, abs(2%) ), 5% )',
	'bs-extjs-theme-btn-toolbar-background-50' => '@bs-extjs-theme-btn-toolbar-background',
	'bs-extjs-theme-btn-toolbar-background-51' => 'darken( @bs-extjs-theme-btn-toolbar-background, 2.5% )',
	'bs-extjs-theme-btn-toolbar-background-100' => '@bs-extjs-theme-btn-toolbar-background',

	//Button in toolbar without icon (upload, Bookmaker "Export section")
	'bs-extjs-theme-btn-toolbar-noicon-text' => '@bs-extjs-theme-secondary-text-color',
	'bs-extjs-theme-btn-toolbar-noicon-border' => '1px solid @bs-extjs-theme-btn-toolbar-noicon-background',
	'bs-extjs-theme-btn-toolbar-noicon-background' => '@bs-extjs-theme-secondary-background-color',
	'bs-extjs-theme-btn-toolbar-noicon-background-0' => 'lighten( saturate( @bs-extjs-theme-btn-toolbar-noicon-background, abs(2%) ), 5% )',
	'bs-extjs-theme-btn-toolbar-noicon-background-50' => '@bs-extjs-theme-btn-toolbar-noicon-background',
	'bs-extjs-theme-btn-toolbar-noicon-background-51' => 'darken( @bs-extjs-theme-btn-toolbar-noicon-background, 2.5% )',
	'bs-extjs-theme-btn-toolbar-noicon-background-100' => '@bs-extjs-theme-btn-toolbar-noicon-background',

	//more stylings
	//window
	'bs-extjs-theme-window-header-text' => '@bs-extjs-theme-primary-text-color',
	'bs-extjs-theme-window-header-background' => '@bs-extjs-theme-primary-background-color-1st-derivate',
	'bs-extjs-theme-window-header-border' => '5px solid @bs-extjs-theme-primary-background-color-1st-derivate',
	'bs-extjs-theme-window-border' => '5px solid @bs-extjs-theme-primary-background-color-1st-derivate',
	'bs-extjs-theme-window-header-border-width' => '5px',
	'bs-extjs-theme-window-background' => 'white',
	'bs-extjs-theme-window-body-background' => 'white',
	'bs-extjs-theme-window-header-border-radius' => '2px 2px 0px 0px',
	'bs-extjs-theme-window-border-radius' => '2px',
	'bs-extjs-theme-window-shadow-radius' => '5px',

	//inner background (i. e. insert link)
	'bs-extjs-theme-layout-background' => '@bs-extjs-theme-primary-background-color-1st-derivate',


	//tab's (i. e. insert link)
	'bs-extjs-theme-tab-default-background' => '@bs-extjs-theme-primary-background-color',
	'bs-extjs-theme-tab-default-text' => '@bs-extjs-theme-primary-text-color',
	'bs-extjs-theme-tab-active-background' => '@bs-extjs-theme-primary-background-color-3rd-derivate',
	'bs-extjs-theme-tab-active-strip' => '@bs-extjs-theme-primary-background-color-3rd-derivate',
	'bs-extjs-theme-tab-active-text' => '@bs-extjs-theme-primary-background-color',
	'bs-extjs-theme-tab-background' => '@bs-extjs-theme-primary-background-color-1st-derivate',

	//toolbar (i. e. insert image, specialpages, footer with OK/Cancel button)
	'bs-extjs-theme-toolbar-background' => 'white',
	'bs-extjs-theme-toolbar-text' => '@bs-extjs-theme-secondary-text-color',
	'bs-extjs-theme-toolbar-footer-background' => '@bs-extjs-theme-primary-background-color-2nd-derivate',
	'bs-extjs-theme-toolbar-footer-text' => '@bs-extjs-theme-primary-text-color',
	'bs-extjs-theme-toolbar-form-item-label-text' => '@bs-extjs-theme-secondary-text-color',

	//panel
	'bs-extjs-theme-panel-header-background' => '@bs-extjs-theme-primary-background-color',
	'bs-extjs-theme-panel-header-text' => '@bs-extjs-theme-primary-text-color',
	'bs-extjs-theme-panel-header-horizontal-border' => '1px solid @bs-extjs-theme-primary-background-color',
	'bs-extjs-theme-panel-header-horizontal-background' => '@bs-extjs-theme-primary-background-color',
	'bs-extjs-theme-panel-header-vertical-border' => '1px solid @bs-extjs-theme-primary-background-color',
	'bs-extjs-theme-panel-header-vertical-background' => '@bs-extjs-theme-primary-background-color',
	'bs-extjs-theme-panel-border' => '5px solid @bs-extjs-theme-primary-background-color',
	'bs-extjs-theme-panel-body-background' => 'white',
	'bs-extjs-theme-panel-form-item-label-text' => '@bs-extjs-theme-secondary-text-color',

	// border tree view ( i. e. insert category)
	'bs-extjs-theme-tree-view-border' => '0px solid black'

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