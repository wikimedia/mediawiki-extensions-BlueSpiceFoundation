<?php

/**
 * BlueSpice for MediaWiki
 * Description: Adds functionality for business needs
 * Authors: Markus Glaser
 *
 * Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
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
 * For further information visit http://bluespice.com
 *
 */
/* Changelog
 */

if( !ExtensionRegistry::getInstance()->isLoaded('BlueSpiceFoundation') ){
	wfLoadExtension('BlueSpiceFoundation');
}

if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/autoload.php' );
}

$wgFooterIcons['poweredby']['bluespice'] = array(
	"src" => "$wgScriptPath/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-poweredby_bluespice_88x31.png",
	"url" => "http://bluespice.com",
	"alt" => "Powered by BlueSpice",
);

require_once( __DIR__."/includes/AutoLoader.php");
require_once( __DIR__."/includes/Defines.php" );
require_once( __DIR__."/includes/DefaultSettings.php" );

// Register hooks
require_once( 'BlueSpice.hooks.php' );
//Setup

// initalise BlueSpice as first extension in a fully initialised environment
array_unshift(
	$wgExtensionFunctions,
	'BsCore::doInitialise'
);

//make old ajax functions available, remove this after replacement implemented
$wgAjaxExportList[] = 'BsCommonAJAXInterface::getTitleStoreData';
$wgAjaxExportList[] = 'BsCommonAJAXInterface::getNamespaceStoreData';
$wgAjaxExportList[] = 'BsCommonAJAXInterface::getUserStoreData';
$wgAjaxExportList[] = 'BsCommonAJAXInterface::getFileUrl';
