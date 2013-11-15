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

//wgScriptPath relative paths
$sResourcesPath = '/extensions/BlueSpiceFoundation/resources';
$bsgExtJSFiles = array(
	'scripts' => array(
		$sResourcesPath.'/extjs/ext-all.js', //This allows us to switch to bootstrap.js
	),
	'debug-scripts' => array(
		$sResourcesPath.'/extjs/ext-all-debug-w-comments.js',
	)
);

$bsgExtJSThemes = array(
	'bluespice' => array(
		'scripts' => array(
			//As bluespice-theme is derived from ext-theme-neptune we need 
			//these JS modifications
			$sResourcesPath.'/extjs/ext-theme-neptune.js'
		),
		'styles' => array(
			$sResourcesPath.'/bluespice.extjs/bluespice-theme/bluespice-theme-all.css'
		),
		'debug-scripts' => array(
			$sResourcesPath.'/extjs/ext-theme-neptune-debug.js'
		),
		'debug-styles' => array(
			$sResourcesPath.'/bluespice.extjs/bluespice-theme/bluespice-theme-all-debug.css'
		)
	),
	'neptune' => array(
		'scripts' => array(
			$sResourcesPath.'/extjs/ext-theme-neptune.js'
		),
		'styles' => array(
			$sResourcesPath.'/extjs/resources/ext-theme-neptune/ext-theme-neptune-all.css'
		),
		'debug-scripts' => array(
			$sResourcesPath.'/extjs/ext-theme-neptune-debug.js'
		),
		'debug-styles' => array(
			$sResourcesPath.'/extjs/resources/ext-theme-neptune/ext-theme-neptune-all-debug.css'
		)
	),
	'classic' => array(
		'styles' => array(
			$sResourcesPath.'/extjs/resources/ext-theme-classic/ext-theme-classic-all.css'
		),
		'debug-styles' => array(
			$sResourcesPath.'/extjs/resources/ext-theme-classic/ext-theme-classic-all-debug.css'
		)
	),
	'gray' => array(
		'styles' => array(
			$sResourcesPath.'/extjs/resources/ext-theme-gray/ext-theme-gray-all.css'
		),
		'debug-styles' => array(
			$sResourcesPath.'/extjs/resources/ext-theme-gray/ext-theme-gray-all-debug.css'
		)
	)
);

$bsgExtJSTheme = 'bluespice';

unset($sResourcesPath);