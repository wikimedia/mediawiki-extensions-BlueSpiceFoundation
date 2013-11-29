<?php

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$aResourceModuleTemplate = array(
	'localBasePath' => $IP . '/extensions/BlueSpiceFoundation/resources',
	'remoteExtPath' => 'BlueSpiceFoundation/resources',
	//'remoteBasePath' => &$GLOBALS['wgScriptPath'],
	'group' => 'ext.bluespice',
);

$wgResourceModules['ext.bluespice'] = array(
	'scripts' => array(
		'bluespice/bs.tools.js',
		'bluespice/bluespice.js',
		'bluespice/bluespice.extensionManager.js',
		'bluespice/bluespice.util.js',
		'bluespice/bluespice.wikiText.js',

		'bluespice/bluespice.string.js',
		'bluespice/bluespice.xhr.js',
		
		'bluespice/bluespice.ping.js',

		'bluespice.libs/slimScroll.min.js',
	),
	'styles' => array(
		'bluespice/bluespice.css',
		'bluespice.extjs/bluespice.extjs.fixes.css'
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
		
		'blanknamespace', //MediaWiki
	),
	'position' => 'top' // available since r85616
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.extjs'] = array(
	'scripts' => array(
		'bluespice.extjs/bluespice.extjs.js',
	),
	//Those are mainly Ext.ux styles that are not part of ext-all.css or the 
	//theme
	'styles' => array(
		'bluespice.extjs/Ext.ux/css/GroupTabPanel.css',
		'bluespice.extjs/Ext.ux/css/ItemSelector.css',
		'bluespice.extjs/Ext.ux/css/LiveSearchGridPanel.css',
		'bluespice.extjs/Ext.ux/css/TabScrollerMenu.css',
		'bluespice.extjs/Ext.ux/form/field/BoxSelect.css'
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
		'bs-extjs-add',
		'bs-extjs-remove',
		'bs-extjs-hint',
		'bs-extjs-error',
		'bs-extjs-confirm',
		'bs-extjs-loading',
		'bs-extjs-pageSize',
		'bs-extjs-actions-column-header',
		'bs-extjs-saving',
		'bs-extjs-warning',
		'bs-extjs-reset',
		'bs-extjs-close',
		'bs-extjs-label-user',
		'bs-extjs-confirmNavigationTitle',
		'bs-extjs-confirmNavigationText',
		'bs-extjs-allns',
		'bs-extjs-upload',
		'bs-extjs-browse',
		'bs-extjs-uploading'
	)
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.extjs.BS.portal'] = array(
	'dependencies' => array(
		'ext.bluespice.extjs'
	),
	'messages' => array(
		'bs-extjs-portal-config',
		'bs-extjs-portal-portlets',
		'bs-extjs-portal-title',
		'bs-extjs-portal-height',
		'bs-extjs-portal-count',
		'bs-extjs-portal-timespan',
		'bs-extjs-portal-timespan-week',
		'bs-extjs-portal-timespan-month',
		'bs-extjs-portal-timespan-alltime'
	)
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.extjs.BS.portal.css'] = array(
	'styles' => array(
		'bluespice.extjs/bluespice.extjs.BS.portal.css'
	),
	'dependencies' => array(
		'ext.bluespice.extjs'
	)
) + $aResourceModuleTemplate;

unset( $aResourceModuleTemplate );