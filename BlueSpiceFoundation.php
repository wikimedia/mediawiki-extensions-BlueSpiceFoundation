<?php

/**
 * BlueSpice for MediaWiki
 * Description:
 * Authors: Markus Glaser
 *
 * Copyright (C) 2010 Hallo Welt! – Medienwerkstatt GmbH, All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * For further information visit http://www.blue-spice.org
 *
 * Version information
 * $LastChangedDate: 2013-06-24 15:24:05 +0200 (Mo, 24 Jun 2013) $
 * $LastChangedBy: rvogel $
 * $Rev: 9876 $
 * $Id: bluespice-core.php 9876 2013-06-24 13:24:05Z rvogel $
 */
/* Changelog
 */
$wgBlueSpiceExtInfo = array(
	'name' => 'BlueSpice for MediaWiki',
	'version' => '1.22.0',
	'url' => 'http://www.blue-spice.org',
	'desc' => 'Extension for MediaWiki to make it more suitable for business needs',
	'author' => array(
		'[http://www.hallowelt.biz Hallo Welt! Medienwerkstatt GmbH]',
	)
);

$wgExtensionCredits['other'][] = array(
	'name' => 'BlueSpice for MediaWiki ' . $wgBlueSpiceExtInfo['version'],
	'svn-date' => '$LastChangedDate: 2013-06-24 15:24:05 +0200 (Mo, 24 Jun 2013) $',
	'svn-revision' => '$LastChangedRevision: 9876 $',
	'description' => $wgBlueSpiceExtInfo['desc'],
	'author' => $wgBlueSpiceExtInfo['author'],
	'url' => $wgBlueSpiceExtInfo['url'],
);

$wgFooterIcons['poweredby']['bluespice'] = array(
	"src" => "$wgScriptPath/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-poweredby_bluespice_88x31.png",
	"url" => "http://blue-spice.org",
	"alt" => "Powered by BlueSpice",
);

require_once( __DIR__."/includes/AutoLoader.php");
require_once( __DIR__."/resources/Resources.php");

//Setup
$wgExtensionFunctions[] = 'BsCoreHooks::setup';

//Hooks
$wgHooks['SoftwareInfo'][]              = 'BsCoreHooks::onSoftwareInfo';
$wgHooks['BeforePageDisplay'][]         = 'BsCoreHooks::onBeforePageDisplay';
$wgHooks['MakeGlobalVariablesScript'][] = 'BsCoreHooks::onMakeGlobalVariablesScript';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'BsCoreHooks::onLoadExtensionSchemaUpdates';
//$wgHooks['MediaWikiPerformAction'][]     = 'BsCoreHooks::doSetup'; //TODO: Move adapter hook handler

$wgAjaxExportList[] = 'BSCommonAJAXInterface::getTitleStoreData';
$wgAjaxExportList[] = 'BSCommonAJAXInterface::getNamespaceStoreData';

if( $wgDBtype == 'oracle' ) {
	$wgHooks['ArticleDelete'][] = 'BSOracleHooks::onArticleDelete';
}

//Default settings needed for BlueSpice
$wgNamespacesWithSubpages[NS_MAIN] = true;
$wgActions['remote'] = 'BsRemoteAction';
$wgApiFrameOptions = 'SAMEORIGIN';
$wgUseAjax = true;

//I18N
$wgExtensionMessagesFiles['BlueSpice'] = __DIR__."/languages/BlueSpice.i18n.php";
$wgExtensionMessagesFiles['Validator'] = __DIR__."/languages/Validator.i18n.php";
$wgExtensionMessagesFiles['BlueSpice.ExtJS'] = __DIR__."/languages/BlueSpice.ExtJS.i18n.php";
$wgExtensionMessagesFiles['BlueSpiceDiagnostics'] = __DIR__."/languages/BlueSpice.Diagnostics.i18n.php";
$wgExtensionMessagesFiles['DiagnosticsAlias'] = __DIR__ . '/languages/BlueSpice.Diagnostics.alias.php'; # Location of an aliases file (Tell MediaWiki to load this file)
$wgSpecialPageGroups['Diagnostics'] = 'bluespice';
$wgSpecialPages['Diagnostics'] = 'SpecialDiagnostics'; # Tell MediaWiki about the new special page and its class name

if (!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);
elseif (DS != DIRECTORY_SEPARATOR) {
	$message = 'Constant "DS" already in use but unequal "DIRECTORY_SEPARATOR", namely: DS == "' . DS . '"';
	//throw new Exception($message);
	exit($message . ' in ' . __FILE__ . ', line ' . __LINE__);
}

// TODO MRG20100724: Ist das ok beim Hosting (index.php ist hier symlinked)
/* Lilu:
 * Bei Symlinks wird es Probleme geben, da __FILE__ den absoluten Pfad inkl. aufgelöster Symlinks enthält.
 * Lösung wäre für das Hosting ein gemeinsam genutzter Core mit separater Konfiguration pro Präsenz.
 * Dies sollte sich ohne Probleme umsetzen lassen, da BlueSpice ja so designed ist, dass der Core in einem
 * separaten Verzeichnis liegen kann.
 */
wfProfileIn('Performance: Core - Defines');
if (!defined('WIKI_FARMING')) {
	define('BSROOTDIR', dirname(__FILE__));
	define('BSCONFIGDIR', BSROOTDIR . DS . 'config');
	define('BSVENDORDIR', BSROOTDIR . DS . 'vendor');
	define('BSDATADIR', BSROOTDIR . DS . 'data'); //Present
	//define('BSDATADIR', "{$wgUploadDirectory}/bluespice"); //Future
	define('BSCACHEDIR', "{$wgFileCacheDirectory}/bluespice"); //$wgCacheDirectory?
}
wfProfileOut('Performance: Core - Defines');

wfProfileIn('Performance: Core - Includes');
require(BSROOTDIR . DS . 'includes' . DS . 'Core.class.php');
require(BSROOTDIR . DS . 'includes' . DS . 'Common.php');
wfProfileOut('Performance: Core - Includes');

/*
 * spl_autoload_register( $autoload_function, $throw, $prepend )
 * Changelog: The $prepend parameter was added with PHP 5.3.0
 */
spl_autoload_register(array('BsCore', 'autoload'), true, true);

BsCore::getInstance('MW')->setup();
$oAdapterMW = BsCore::getInstance('MW')->getAdapter();
$wgHooks['MediaWikiPerformAction'][] = array( $oAdapterMW, 'doSetup' );
$wgHooks['UserGetRights'][] = array( $oAdapterMW, 'onUserGetRights' );
$wgHooks['userCan'][]       = array( $oAdapterMW, 'onUserCan' );
$wgHooks['FormDefaults'][]  = array($oAdapterMW, 'onFormDefaults');
$wgHooks['UserAddGroup'][]  = array( $oAdapterMW, 'addTemporaryGroupToUserHelper' );
$wgHooks['UploadVerification'][] = array($oAdapterMW, 'onUploadVerification');
$wgHooks['EditPage::showEditForm:initial'][] = array($oAdapterMW, 'onEditPageShowEditFormInitial');
array_unshift(
	$wgHooks['EditPage::showEditForm:initial'], 
	array($oAdapterMW, 'lastChanceBehaviorSwitches')
);
$wgHooks['ArticleAfterFetchContent'][] = array($oAdapterMW, 'behaviorSwitches');
$wgHooks['ParserBeforeStrip'][] = array($oAdapterMW, 'hideBehaviorSwitches');
$wgHooks['ParserBeforeTidy'][]  = array($oAdapterMW, 'recoverBehaviorSwitches');

//BlueSpice specific hooks
$wgHooks['BSBlueSpiceSkinAfterArticleContent'][] = array($oAdapterMW, 'onBlueSpiceSkinAfterArticleContent');

// initalise BlueSpice as first extension in a fully initialised environment
array_unshift($wgExtensionFunctions, array( BsCore::getInstance('MW')->getAdapter(), 'doInitialise' ));
