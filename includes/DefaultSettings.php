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
if ( !isset( $GLOBALS['wgFooterIcons']['poweredby']['bluespice'] ) ) {
	$GLOBALS['wgFooterIcons']['poweredby']['bluespice'] = array(
		"src" => $GLOBALS['wgScriptPath'] . "/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-poweredby_bluespice_88x31.png",
		"url" => "http://bluespice.com",
		"alt" => "Powered by BlueSpice",
	);
}

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

if ( !isset( $GLOBALS[ 'bsgGroupRoles' ] ) ) {
	$GLOBALS['bsgGroupRoles'] = [];
}

$GLOBALS[ 'bsgGroupRoles' ] = array_merge( [
	'bureaucrat' => [ 'accountmanager' => true ],
	'sysop' => [ 'admin' => true ],
	'user' => [ 'editor' => true ],
	'*' => [ 'reader' => true ]
], $GLOBALS['bsgGroupRoles'] );

if ( !isset( $GLOBALS[ 'bsgNamespaceRolesLockdown' ] ) ) {
	$GLOBALS['bsgNamespaceRolesLockdown'] = [];
}

$GLOBALS[ 'bsgPermissionConfigDefault' ] = [
	"apihighlimits" => [
		"type" => 'global',
		"roles" => [ 'bot', 'maintenanceadmin' ]
	],
	"applychangetags" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'author', 'editor', 'maintenanceadmin', 'reviewer' ]
	],
	"autoconfirmed" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'author', 'bot', 'editor', 'maintenanceadmin', 'reviewer', 'structuremanager' ]
	],
	"autocreateaccount" => [
		"type" => 'global',
		"roles" => [ 'reader' ]
	],
	"autopatrol" => [
		"type" => 'global',
		"roles" => [ 'bot', 'editor', 'maintenanceadmin' ]
	],
	"bigdelete" => [
		"type" => 'global',
		"roles" => [ 'admin', 'maintenanceadmin' ]
	],
	"block" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'maintenanceadmin' ]
	],
	"blockemail" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'maintenanceadmin' ]
	],
	"bot" => [
		"type" => 'global',
		"roles" => [ 'bot', 'maintenanceadmin' ]
	],
	"browsearchive" => [
		"type" => 'global',
		"roles" => [ 'admin', 'editor', 'maintenanceadmin' ]
	],
	"changetags" => [
		"type" => 'global',
		"roles" => [ 'admin', 'author', 'editor', 'maintenanceadmin', 'reviewer' ]
	],
	"createaccount" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'accountselfcreate', 'admin', 'maintenanceadmin' ]
	],
	"createpage" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'author', 'editor', 'maintenanceadmin' ]
	],
	"createtalk" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'author', 'commenter', 'editor', 'maintenanceadmin' ]
	],
	"delete" => [
		"type" => 'namespace',
		"preventLockout" => '1',
		"roles" => [ 'admin', 'editor', 'maintenanceadmin' ]
	],
	"deletechangetags" => [
		"type" => 'global',
		"roles" => [ 'admin', 'editor', 'maintenanceadmin' ]
	],
	"deletedhistory" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'editor', 'maintenanceadmin' ]
	],
	"deletedtext" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'author', 'editor', 'maintenanceadmin', 'reviewer' ]
	],
	"edit" => [
		"type" => 'namespace',
		"preventLockout" => '1',
		"roles" => [ 'admin', 'author', 'editor', 'maintenanceadmin' ]
	],
	"editcontentmodel" => [
		"type" => 'global',
		"roles" => [ 'bot', 'maintenanceadmin' ]
	],
	"editinterface" => [
		"type" => 'global',
		"roles" => [ 'admin', 'maintenanceadmin', 'structuremanager' ]
	],
	"editmyoptions" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'author', 'bot', 'commenter', 'editor', 'maintenanceadmin', 'reader', 'reviewer', 'structuremanager' ]
	],
	"editmyprivateinfo" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'author', 'bot', 'commenter', 'editor', 'maintenanceadmin', 'reader', 'reviewer', 'structuremanager' ]
	],
	"editmyusercss" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'author', 'bot', 'commenter', 'editor', 'maintenanceadmin', 'reader', 'reviewer', 'structuremanager' ]
	],
	"editmyuserjs" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'author', 'bot', 'commenter', 'editor', 'maintenanceadmin', 'reader', 'reviewer', 'structuremanager' ]
	],
	"editmyuserjson" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'author', 'bot', 'commenter', 'editor', 'maintenanceadmin', 'reader', 'reviewer', 'structuremanager' ]
	],
	"editmywatchlist" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'author', 'bot', 'commenter', 'editor', 'maintenanceadmin', 'reader', 'reviewer', 'structuremanager' ]
	],
	"editprotected" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'editor', 'maintenanceadmin' ]
	],
	"editsemiprotected" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'editor', 'maintenanceadmin' ]
	],
	"editusercss" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'maintenanceadmin' ]
	],
	"edituserjs" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'maintenanceadmin' ]
	],
	"edituserjson" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'maintenanceadmin' ]
	],
	"hideuser" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'maintenanceadmin' ]
	],
	"import" => [
		"type" => 'global',
		"roles" => [ 'admin', 'bot', 'editor', 'maintenanceadmin' ]
	],
	"importupload" => [
		"type" => 'global',
		"roles" => [ 'admin', 'bot', 'editor', 'maintenanceadmin' ]
	],
	"ipblock-exempt" => [
		"type" => 'global',
		"roles" => [ 'admin', 'maintenanceadmin' ]
	],
	"managechangetags" => [
		"type" => 'global',
		"roles" => [ 'admin', 'maintenanceadmin' ]
	],
	"markbotedits" => [
		"type" => 'global',
		"roles" => [ 'admin', 'bot', 'maintenanceadmin' ]
	],
	"mergehistory" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'maintenanceadmin' ]
	],
	"minoredit" => [
		"type" => 'global',
		"roles" => [ 'admin', 'author', 'editor', 'maintenanceadmin' ]
	],
	"move" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'editor', 'maintenanceadmin', 'structuremanager' ]
	],
	"move-categorypages" => [
		"type" => 'global',
		"roles" => [ 'admin', 'editor', 'maintenanceadmin', 'structuremanager' ]
	],
	"move-rootuserpages" => [
		"type" => 'global',
		"roles" => [ 'admin', 'maintenanceadmin', 'structuremanager' ]
	],
	"move-subpages" => [
		"type" => 'global',
		"roles" => [ 'editor', 'maintenanceadmin', 'structuremanager' ]
	],
	"movefile" => [
		"type" => 'global',
		"roles" => [ 'admin', 'editor', 'maintenanceadmin', 'structuremanager' ]
	],
	"nominornewtalk" => [
		"type" => 'global',
		"roles" => [ 'bot', 'maintenanceadmin' ]
	],
	"noratelimit" => [
		"type" => 'global',
		"roles" => [ 'bot', 'maintenanceadmin' ]
	],
	"override-export-depth" => [
		"type" => 'global',
		"roles" => [ 'admin', 'maintenanceadmin' ]
	],
	"passwordreset" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'maintenanceadmin' ]
	],
	"patrol" => [
		"type" => 'namespace',
		"roles" => [ 'maintenanceadmin' ]
	],
	"patrolmarks" => [
		"type" => 'global',
		"roles" => [ 'maintenanceadmin' ]
	],
	"protect" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'editor', 'maintenanceadmin' ]
	],
	"purge" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'author', 'editor', 'maintenanceadmin' ]
	],
	"read" => [
		"type" => 'namespace',
		"preventLockout" => '1',
		"roles" => [ 'accountmanager', 'admin', 'author', 'bot', 'commenter', 'editor', 'maintenanceadmin', 'reader', 'reviewer', 'structuremanager' ]
	],
	"reupload" => [
		"type" => 'global',
		"roles" => [ 'admin', 'author', 'editor', 'maintenanceadmin' ]
	],
	"reupload-shared" => [
		"type" => 'global',
		"roles" => [ 'admin', 'author', 'editor', 'maintenanceadmin' ]
	],
	"rollback" => [
		"type" => 'global',
		"roles" => [ 'admin', 'editor', 'maintenanceadmin' ]
	],
	"sendemail" => [
		"type" => 'global',
		"roles" => [ 'maintenanceadmin' ]
	],
	"suppressredirect" => [
		"type" => 'global',
		"roles" => [ 'admin', 'author', 'editor', 'maintenanceadmin', 'reviewer', 'structuremanager' ]
	],
	"unblockself" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'maintenanceadmin' ]
	],
	"undelete" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'editor', 'maintenanceadmin', 'structuremanager' ]
	],
	"unwatchedpages" => [
		"type" => 'global',
		"roles" => [ 'admin', 'maintenanceadmin' ]
	],
	"upload" => [
		"type" => 'global',
		"roles" => [ 'admin', 'author', 'editor', 'maintenanceadmin', 'structuremanager' ]
	],
	"userrights" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'maintenanceadmin' ]
	],
	"viewmyprivateinfo" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'author', 'bot', 'commenter', 'editor', 'maintenanceadmin', 'reader', 'reviewer', 'structuremanager' ]
	],
	"viewmywatchlist" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'author', 'bot', 'commenter', 'editor', 'maintenanceadmin', 'reader', 'reviewer', 'structuremanager' ]
	],
	"wikiadmin" => [
		"type" => 'global',
		"preventLockout" => '1',
		"roles" => [ 'admin', 'maintenanceadmin' ]
	],
	"writeapi" => [
		"type" => 'global',
		"roles" => [ "accountmanager", "accountselfcreate", "admin", "author", "bot", "commenter", "editor", "maintenanceadmin", "reviewer", "structuremanager" ]
	],
];

/**
 * Allows extensions to distinguish between normal content NS, that can be
 * renamed of deleted and system NS that can not be modified. Used in
 * BlueSpiceExtensions/NamespaceManager and NamespaceHelper
 */
$GLOBALS['bsgSystemNamespaces'] = array(
	//1599 => 'NS_COOL_STUFF'
);

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
