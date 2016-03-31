<?php

/**
 * BlueSpice for MediaWiki
 * Description: Adds functionality for business needs
 * Authors: Markus Glaser
 *
 * Copyright (C) 2013 Hallo Welt! – Medienwerkstatt GmbH, All rights reserved.
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
 */
/* Changelog
 */
$wgBlueSpiceExtInfo = array(
	'name' => 'BlueSpice for MediaWiki',
	'version' => '2.23.2',
	'status' => 'stable',
	'package' => 'BlueSpice Free', //default value for BS free extensions
	'url' => 'http://www.blue-spice.org',
	'desc' => 'Makes MediaWiki enterprise ready.',
	'author' => array(
		'[http://www.hallowelt.com Hallo Welt! Medienwerkstatt GmbH]',
	)
);

$wgExtensionCredits['other'][] = array(
	'name' => 'BlueSpice for MediaWiki ' . $wgBlueSpiceExtInfo['version'],
	'description' => $wgBlueSpiceExtInfo['desc'],
	'author' => $wgBlueSpiceExtInfo['author'],
	'url' => $wgBlueSpiceExtInfo['url'],
);

$wgFooterIcons['poweredby']['bluespice'] = array(
	"src" => "$wgScriptPath/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-poweredby_bluespice_88x31.png",
	"url" => "http://bluespice.com",
	"alt" => "Powered by BlueSpice",
);

require_once( __DIR__."/includes/AutoLoader.php");
require_once( __DIR__."/includes/Defines.php" );
require_once( __DIR__."/includes/DefaultSettings.php" );
require_once( __DIR__."/resources/Resources.php");

$wgAjaxExportList[] = 'BsCommonAJAXInterface::getTitleStoreData';
$wgAjaxExportList[] = 'BsCommonAJAXInterface::getNamespaceStoreData';
$wgAjaxExportList[] = 'BsCommonAJAXInterface::getUserStoreData';
$wgAjaxExportList[] = 'BsCommonAJAXInterface::getCategoryStoreData';
$wgAjaxExportList[] = 'BsCommonAJAXInterface::getAsyncCategoryTreeStoreData';
$wgAjaxExportList[] = 'BsCommonAJAXInterface::getFileUrl';
$wgAjaxExportList[] = 'BsCore::ajaxBSPing';

$wgAPIModules['bs-filebackend-store'] = 'BSApiFileBackendStore';
$wgAPIModules['bs-user-store'] = 'BSApiUserStore';
$wgAPIModules['bs-wikipage-tasks'] = 'BSApiWikiPageTasks';

//I18N MW1.23+
$wgMessagesDirs['BlueSpice'] = __DIR__ . '/i18n/core';
$wgMessagesDirs['BlueSpiceCredits'] = __DIR__ . '/i18n/credits';
$wgMessagesDirs['BlueSpiceDiagnostics'] = __DIR__ . '/i18n/diagnostics';
$wgMessagesDirs['BlueSpice.ExtJS'] = __DIR__ . '/i18n/extjs';
$wgMessagesDirs['BlueSpice.ExtJS.Portal'] = __DIR__ . '/i18n/extjs-portal';
$wgMessagesDirs['BlueSpice.Deferred'] = __DIR__ . '/i18n/deferred';
$wgMessagesDirs['Validator'] = __DIR__ . '/i18n/validator';
$wgMessagesDirs['Notifications'] = __DIR__ . '/i18n/notifications';
$wgMessagesDirs['BlueSpice.API'] = __DIR__ . '/i18n/api';

//I18N Backwards compatibility
$wgExtensionMessagesFiles += array(
	'BlueSpice' => __DIR__."/languages/BlueSpice.i18n.php",
	'Validator' => __DIR__."/languages/Validator.i18n.php",
	'BlueSpice.ExtJS' => __DIR__."/languages/BlueSpice.ExtJS.i18n.php",
	'BlueSpice.ExtJS.Portal' => __DIR__."/languages/BlueSpice.ExtJS.Portal.i18n.php",
	'BlueSpice.Deferred' => __DIR__.'/languages/BlueSpice.Deferred.i18n.php',
	'BlueSpiceDiagnostics' => __DIR__."/languages/BlueSpice.Diagnostics.i18n.php",
	'DiagnosticsAlias' => __DIR__."/languages/BlueSpice.Diagnostics.alias.php",
	'BlueSpiceCredits' => __DIR__."/languages/BlueSpice.Credits.i18n.php",
	'CreditsAlias' => __DIR__."/languages/BlueSpice.Credits.alias.php"
);

#$wgSpecialPages['Diagnostics'] = 'SpecialDiagnostics';
$wgSpecialPages['SpecialCredits'] = 'SpecialCredits';

// Register hooks
require_once( 'BlueSpice.hooks.php' );
//Setup

$wgExtensionFunctions[] = 'BsCoreHooks::setup';

// initalise BlueSpice as first extension in a fully initialised environment
array_unshift(
	$wgExtensionFunctions,
	'BsCore::doInitialise'
);
