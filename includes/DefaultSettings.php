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
	$GLOBALS['wgFooterIcons']['poweredby']['bluespice'] = [
		"src" => $GLOBALS['wgScriptPath']
			. "/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-poweredby_bluespice_88x31.png",
		"url" => "https://bluespice.com",
		"alt" => "Powered by BlueSpice",
	];
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

/*
 * Used by BlueSpiceGroupManager to flag custom groups
 * Do not fill this array elsewhere, otherwise GroupManager will get
 * confused!
 */
if ( !isset( $GLOBALS[ 'wgAdditionalGroups' ] ) ) {
	$GLOBALS['wgAdditionalGroups'] = [];
}

/*
 * Used to filter groups according to types.
 * Possible types are
 * * implicit: Groups that are assigned by the system, such as * and user
 * * core-minimal: MediaWiki groups that are needed for a proper rights setup
 * * core-extended: MediaWiki groups that should not be used with BlueSpice
 * * extension-minimal: Groups by extensions that are needed for a proper rights setup
 * * extension-extended: Groups by exensions that should not be used
 * * custom: Groups that are set up by the customer locally
 */
if ( !isset( $GLOBALS[ 'wgGroupTypes' ] ) ) {
	$GLOBALS['wgGroupTypes'] = [];
}

$GLOBALS[ 'bsgEnableRoleSystem' ] = false;

if ( !isset( $GLOBALS[ 'bsgGroupRoles' ] ) ) {
	$GLOBALS['bsgGroupRoles'] = [];
}

$GLOBALS[ 'bsgGroupRoles' ] = array_merge( [
	'bureaucrat' => [ 'accountmanager' => true ],
	'sysop' => [
		'reader' => true,
		'editor' => true,
		'reviewer' => true,
		'admin' => true
	],
	'user' => [ 'editor' => true ],
	'editor' => [
		'reader' => true,
		'editor' => true
	],
	'reviewer' => [
		'reader' => true,
		'editor' => true,
		'reviewer' => true
	],
	'bot' => [
		'bot' => true
	]
], $GLOBALS['bsgGroupRoles'] );

// If "reader" is not explicitly set to "*"
if ( !isset( $GLOBALS['bsgGroupRoles']['*']['reader'] ) ) {
	// respect the setting of wgGroupPermission
	$isPrivate = isset( $GLOBALS['wgGroupPermissions']['*']['read'] ) ?
		!$GLOBALS['wgGroupPermissions']['*']['read'] :
		false;

	$GLOBALS['bsgGroupRoles']['*']['reader'] = !$isPrivate;
} elseif ( $GLOBALS['bsgGroupRoles']['*']['reader'] === false ) {
	// otherwise, if "*" is explicitly denied "reader", give it to "user".
	$GLOBALS['bsgGroupRoles']['user']['reader'] = true;
}

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
		"roles" => [
			'accountmanager',
			'admin',
			'author',
			'bot',
			'editor',
			'maintenanceadmin',
			'reviewer',
			'structuremanager'
		]
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
		"roles" => [ 'bot' ]
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
		"roles" => [ 'admin', 'author', 'maintenanceadmin', 'reviewer' ]
	],
	"deletelogentry" => [
		"type"  => 'global',
		"roles" => [ 'admin' ]
	],
	"deleterevision" => [
		"type"  => 'global',
		"roles" => [ 'admin' ]
	],
	"edit" => [
		"type" => 'namespace',
		"preventLockout" => '1',
		"roles" => [ 'editor' ]
	],
	"editcontentmodel" => [
		"type" => 'global',
		"roles" => [ 'bot', 'maintenanceadmin' ]
	],
	"editinterface" => [
		"type" => 'global',
		"roles" => [ 'admin', 'maintenanceadmin', 'structuremanager' ]
	],
	"editsitecss" => [
		"type" => 'global',
		"roles" => [ 'admin', 'maintenanceadmin', 'structuremanager' ]
	],
	"editsitejs" => [
		"type" => 'global',
		"roles" => [ 'admin', 'maintenanceadmin', 'structuremanager' ]
	],
	"editsitejson" => [
		"type" => 'global',
		"roles" => [ 'admin', 'maintenanceadmin', 'structuremanager' ]
	],
	"editmyoptions" => [
		"type" => 'global',
		"roles" => [
			'accountmanager',
			'admin',
			'author',
			'bot',
			'commenter',
			'editor',
			'maintenanceadmin',
			'reader',
			'reviewer',
			'structuremanager'
		]
	],
	"editmyusercss" => [
		"type" => 'global',
		"roles" => [
			'accountmanager',
			'admin',
			'author',
			'bot',
			'commenter',
			'editor',
			'maintenanceadmin',
			'reader',
			'reviewer',
			'structuremanager'
		]
	],
	"editmyuserjs" => [
		"type" => 'global',
		"roles" => [
			'accountmanager',
			'admin',
			'author',
			'bot',
			'commenter',
			'editor',
			'maintenanceadmin',
			'reader',
			'reviewer',
			'structuremanager'
		]
	],
	"editmyuserjson" => [
		"type" => 'global',
		"roles" => [
			'accountmanager',
			'admin',
			'author',
			'bot',
			'commenter',
			'editor',
			'maintenanceadmin',
			'reader',
			'reviewer',
			'structuremanager'
		]
	],
	"editmywatchlist" => [
		"type" => 'global',
		"roles" => [
			'accountmanager',
			'admin',
			'author',
			'bot',
			'commenter',
			'editor',
			'maintenanceadmin',
			'reader',
			'reviewer',
			'structuremanager'
		]
	],
	"editprotected" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'maintenanceadmin' ]
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
		"roles" => [ 'reader' ]
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
		"roles" => [
			'admin',
			'author',
			'editor',
			'maintenanceadmin',
			'reviewer',
			'structuremanager'
		]
	],
	"unblockself" => [
		"type" => 'global',
		"roles" => [ 'accountmanager', 'admin', 'maintenanceadmin' ]
	],
	"undelete" => [
		"type" => 'namespace',
		"roles" => [ 'admin', 'maintenanceadmin', 'structuremanager' ]
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
		"roles" => [
			'accountmanager',
			'admin',
			'author',
			'bot',
			'commenter',
			'editor',
			'maintenanceadmin',
			'reader',
			'reviewer',
			'structuremanager'
		]
	],
	"viewmywatchlist" => [
		"type" => 'global',
		"roles" => [
			'accountmanager',
			'admin',
			'author',
			'bot',
			'commenter',
			'editor',
			'maintenanceadmin',
			'reader',
			'reviewer',
			'structuremanager'
		]
	],
	"wikiadmin" => [
		"type" => 'global',
		"preventLockout" => '1',
		"roles" => [ 'admin', 'maintenanceadmin' ]
	],
	"editor" => [
		"type" => 'global',
		"roles" => [ "editor", "maintenanceadmin", "admin" ]
	]
];

/**
 * Allows extensions to distinguish between normal content NS, that can be
 * renamed of deleted and system NS that can not be modified. Used in
 * BlueSpiceExtensions/NamespaceManager and NamespaceHelper
 */
$GLOBALS['bsgSystemNamespaces'] = [
	// 1599 => 'NS_COOL_STUFF'
];

/**
 * BsExtensionManager extension registration
 */
$GLOBALS['bsgExtensions'] = [];

/**
 * TemplateHelper template directory overwrite
 * $bsgTemplates = array(
 *    BSExtension.Template.Name": "$wgExtensionsDirectory/MyExtension/PathToTemplateDir",
 * )
 */
$GLOBALS['bsgTemplates'] = [];

$GLOBALS['bsgUserMiniProfileParams'] = [ 'width' => 40, 'height' => 40 ];
$GLOBALS['bsgMiniProfileEnforceHeight'] = true;
// For B/C
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
