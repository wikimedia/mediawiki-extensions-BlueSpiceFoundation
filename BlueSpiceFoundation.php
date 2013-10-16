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
	'version' => '2.22.0',
	'url' => 'http://www.blue-spice.org',
	'desc' => 'Extension for MediaWiki to make it more suitable for business needs',
	'author' => array(
		'[http://www.hallowelt.biz Hallo Welt! Medienwerkstatt GmbH]',
	)
);

$wgExtensionCredits['other'][] = array(
	'name'        => 'BlueSpice for MediaWiki ' . $wgBlueSpiceExtInfo['version'],
	'description' => $wgBlueSpiceExtInfo['desc'],
	'author'      => $wgBlueSpiceExtInfo['author'],
	'url'         => $wgBlueSpiceExtInfo['url'],
);

$wgFooterIcons['poweredby']['bluespice'] = array(
	"src" => "$wgScriptPath/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-poweredby_bluespice_88x31.png",
	"url" => "http://blue-spice.org",
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

//I18N
$wgExtensionMessagesFiles += array(
	'BlueSpice'              => __DIR__."/languages/BlueSpice.i18n.php",
	'Validator'              => __DIR__."/languages/Validator.i18n.php",
	'BlueSpice.ExtJS'        => __DIR__."/languages/BlueSpice.ExtJS.i18n.php",
	'BlueSpice.ExtJS.Portal' => __DIR__."/languages/BlueSpice.ExtJS.Portal.i18n.php",
	'BlueSpiceDiagnostics'   => __DIR__."/languages/BlueSpice.Diagnostics.i18n.php",
	'DiagnosticsAlias'       => __DIR__."/languages/BlueSpice.Diagnostics.alias.php"
);

//$wgSpecialPageGroups['Diagnostics'] = 'bluespice';
//$wgSpecialPages['Diagnostics'] = 'SpecialDiagnostics';

$oCore = BsCore::getInstance();
$wgHooks['SetupAfterCache'][]           = 'BsCoreHooks::onSetupAfterCache';
$wgHooks['SoftwareInfo'][]              = 'BsCoreHooks::onSoftwareInfo';
$wgHooks['BeforePageDisplay'][]         = 'BsCoreHooks::onBeforePageDisplay';
$wgHooks['MakeGlobalVariablesScript'][] = 'BsCoreHooks::onMakeGlobalVariablesScript';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'BsCoreHooks::onLoadExtensionSchemaUpdates';
$wgHooks['ApiCheckCanExecute'][] = 'BsCoreHooks::onApiCheckCanExecute';
$wgHooks['UserGetRights'][] = array( $oCore, 'onUserGetRights' );
$wgHooks['userCan'][]       = array( $oCore, 'onUserCan' );
$wgHooks['FormDefaults'][]  = array( $oCore, 'onFormDefaults' );
$wgHooks['UserAddGroup'][]  = array( $oCore, 'addTemporaryGroupToUserHelper' );
$wgHooks['UploadVerification'][] = array( $oCore, 'onUploadVerification' );
$wgHooks['ArticleAfterFetchContent'][] = array( $oCore, 'behaviorSwitches' );
$wgHooks['ParserBeforeStrip'][] = array( $oCore, 'hideBehaviorSwitches' );
$wgHooks['ParserBeforeTidy'][]  = array( $oCore, 'recoverBehaviorSwitches' );
if( !isset( $wgHooks['EditPage::showEditForm:initial'] ) ) {
	$wgHooks['EditPage::showEditForm:initial'] = array();
}
array_unshift(
	$wgHooks['EditPage::showEditForm:initial'], 
	array( $oCore, 'lastChanceBehaviorSwitches' )
);

//BlueSpice specific hooks
$wgHooks['BSBlueSpiceSkinAfterArticleContent'][] = array( $oCore, 'onBlueSpiceSkinAfterArticleContent' );

if ( $wgDBtype == 'oracle' ) {
	$wgHooks['ArticleDelete'][] = 'BSOracleHooks::onArticleDelete';
}

//Setup
$wgExtensionFunctions[] = 'BsCoreHooks::setup';
// initalise BlueSpice as first extension in a fully initialised environment
array_unshift(
	$wgExtensionFunctions, 
	array( $oCore, 'doInitialise' )
);
