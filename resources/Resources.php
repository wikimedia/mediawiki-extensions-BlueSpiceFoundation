<?php
$bsFoundation = 'extensions/BlueSpiceFoundation';

$wgResourceModules['ext.bluespice'] = array(
	'scripts' => array(
		$bsFoundation.'/resources/bluespice/bs.tools.js',
		$bsFoundation.'/resources/bluespice/bluespice.js',
		$bsFoundation.'/resources/bluespice/bluespice.util.js',
		$bsFoundation.'/resources/bluespice/bluespice.wikiText.js',

		$bsFoundation.'/resources/bluespice/bluespice.string.js',
		$bsFoundation.'/resources/bluespice/bluespice.xhr.js',

		$bsFoundation.'/resources/bluespice.libs/slimScroll.min.js',
	),
	'styles' => array(
		$bsFoundation.'/resources/bluespice/bluespice.css',
		$bsFoundation.'/resources/bluespice.extjs/bluespice.extjs.fixes.css'
	),
	'dependencies' => array(
		'jquery', 'jquery.ui.core',
		'jquery.ui.dialog',
		'jquery.ui.tabs',
		'jquery.cookie',
		'jquery.ui.sortable',
		'jquery.ui.autocomplete',
		'jquery.effects.core',
		'mediawiki.legacy.shared',
		'mediawiki.action.history.diff',
		'mediawiki.page.ready'
	),
	'messages' => array(
		'largefileserver',
		'bs-year-duration',
		'bs-years-duration',
		'bs-month-duration',
		'bs-months-duration',
		'bs-week-duration',
		'bs-weeks-duration',
		'bs-day-duration',
		'bs-days-duration',
		'bs-hour-duration',
		'bs-hours-duration',
		'bs-min-duration',
		'bs-mins-duration',
		'bs-sec-duration',
		'bs-secs-duration',
		'bs-two-units-ago',
		'bs-one-unit-ago',
		'bs-now',
	),
	'localBasePath' => $IP,
	'remoteBasePath' => &$GLOBALS['wgScriptPath'],
	'position' => 'top' // available since r85616
);
$wgResourceModules['ext.bluespice.extjs'] = array(
	'scripts' => array(
		$bsFoundation.'/resources/bluespice.extjs/bluespice.extjs.js',
		$bsFoundation.'/resources/bluespice.extjs/Ext.ux/FitToParent.js',
	),
	'dependencies' => array(
		'ext.bluespice'
	),
	'messages' => array(
		'bs-extjs-ok',
		'bs-extjs-cancel',
		'bs-extjs-yes',
		'bs-extjs-no',
		'bs-extjs-save',
		'bs-extjs-delete',
		'bs-extjs-hint',
		'bs-extjs-error',
		'bs-extjs-confirm',
		'bs-extjs-loading',
	)
);