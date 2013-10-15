<?php

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
	'scripts-debug' => array(
		$sResourcesPath.'/extjs/ext-all-debug-w-comments.js',
	),
	'styles' => array(),
	'styles-debug' => array()
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