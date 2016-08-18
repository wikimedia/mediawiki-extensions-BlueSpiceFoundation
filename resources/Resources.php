<?php

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$aResourceModuleTemplate = array(
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'BlueSpiceFoundation/resources',
	'targets' => array( 'mobile', 'desktop' )
);

$wgResourceModules['ext.bluespice'] = array(
	'scripts' => array(
		'bluespice/bluespice.js',
		'bluespice/bluespice.extensionManager.js',
		'bluespice/bluespice.util.js',
		'bluespice/bluespice.wikiText.js',
		'bluespice/bluespice.string.js',
		'bluespice/bluespice.xhr.js',
		'bluespice/bluespice.ping.js',
		'bluespice/bluespice.tooltip.js',
		'bluespice/bluespice.api.js'
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
	)
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.styles'] = array(
	'styles' => array(
		'bluespice/bluespice.css',
		'bluespice/bluespice.icons.css',
		'bluespice/bluespice.ui.basic.less'
	),
	'position' => 'top'
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.extjs.base'] = array(
	'class' => 'ResourceLoaderExtJSModule' //Provides framework JS and I18N
) + $aResourceModuleTemplate;

//TODO: Implement as subclass of ResourceLoaderFileModule to provide RTL support
$wgResourceModules['ext.bluespice.extjs.theme'] = array(
	'scripts' => array(
		//Some skin specific overrides. As "bluespice-theme" derives from
		//ExtJS's "neptune" we need to include this framework file
		'extjs/ext-theme-neptune-debug.js'
	),
	'styles' => array(
		//Custom build ot ExtJS's "neptune" theme
		'bluespice.extjs/bluespice-theme/bluespice-theme-all.css',
	),
	'dependencies' => array(
		//Yes, the theme depends on the framework, not the other way round.
		//This is because the theme may have JS that depends on the framework.
		//If we didn't have it this way we would need to specify a seperate
		//'scripts' RL module for the theme.
		'ext.bluespice.extjs.base'
	),
	'group' => 'bsextjs'
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.extjs.theme.ux'] = array(
	'styles' => array(
		//Mainly Ext.ux styles that are not part of the theme
		'bluespice.extjs/Ext.ux/css/GroupTabPanel.css',
		'bluespice.extjs/Ext.ux/css/ItemSelector.css',
		'bluespice.extjs/Ext.ux/css/LiveSearchGridPanel.css',
		'bluespice.extjs/Ext.ux/css/TabScrollerMenu.css',
		'bluespice.extjs/Ext.ux/grid/css/GridFilters.css',
		'bluespice.extjs/Ext.ux/grid/css/RangeMenu.css',
		'bluespice.extjs/Ext.ux/form/field/BoxSelect.css'
	),
	'group' => 'bsextjs'
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.extjs'] = array(
	'scripts' => array(
		'bluespice.extjs/bluespice.extjs.js',
		'bluespice.extjs/BS/override/grid/column/Action.js'
	),
	'styles' => array(
		//There are some weird legacy CSS fixes. Don't know if they still apply
		'bluespice.extjs/bluespice.extjs.fixes.css',
		'bluespice.extjs/bluespice.extjs.overrides.less'
	),
	'messages' => array(
		'bs-extjs-ok',
		'bs-extjs-cancel',
		'bs-extjs-yes',
		'bs-extjs-no',
		'bs-extjs-save',
		'bs-extjs-delete',
		'bs-extjs-edit',
		'bs-extjs-add',
		'bs-extjs-remove',
		'bs-extjs-copy',
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
		'bs-extjs-label-namespace',
		'bs-extjs-label-page',
		'bs-extjs-confirmNavigationTitle',
		'bs-extjs-confirmNavigationText',
		'bs-extjs-allns',
		'bs-extjs-upload',
		'bs-extjs-browse',
		'bs-extjs-uploading',
		'bs-extjs-filters',
		'bs-extjs-filter-equals',
		'bs-extjs-filter-equals-not',
		'bs-extjs-filter-contains',
		'bs-extjs-filter-contains-not',
		'bs-extjs-filter-starts-with',
		'bs-extjs-filter-ends-with',
		'bs-extjs-filter-greater-than',
		'bs-extjs-filter-less-than',
		'bs-extjs-title-success',
		'bs-extjs-title-warning',
		'bs-extjs-filter-bool-yes',
		'bs-extjs-filter-bool-no',
		'bs-extjs-categoryboxselect-emptytext'
	),
	'dependencies' => array(
		'ext.bluespice.extjs.theme.ux',
		'ext.bluespice.extjs.theme'
	),
	'group' => 'bsextjs'
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.extjs.BS.portal'] = array(
	'dependencies' => array(
		'ext.bluespice.extjs'
	),
	'messages' => array(
		'bs-extjs-portal-config',
		'bs-extjs-portal-title',
		'bs-extjs-portal-height',
		'bs-extjs-portal-count',
		'bs-extjs-portal-timespan',
		'bs-extjs-portal-timespan-week',
		'bs-extjs-portal-timespan-month',
		'bs-extjs-portal-timespan-alltime'
	)
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.extjs.BS.deferred'] = array(
	'dependencies' => array(
		'ext.bluespice.extjs'
	),
	'messages' => array(
		'bs-deferred-action-status-pending',
		'bs-deferred-action-status-running',
		'bs-deferred-action-status-done',
		'bs-deferred-action-status-error',
		'bs-deferred-action-apicopypage-description',
		'bs-deferred-action-apieditpage-description',
		'bs-deferred-batch-title',
		'bs-deferred-batch-progress-desc',
		'bs-deferred-batch-actions',
		'bs-deferred-batch-description',
		'bs-deferred-batch-status'
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

$wgResourceModules['ext.bluespice.html.formfields.sortable'] = array(
	'scripts' => array(
		'bluespice/bluespice.html.formfields.sortable.js'
	),
	'styles' => array(
		'bluespice/bluespice.html.formfields.sortable.css'
	),
	'dependencies' => array(
		'ext.bluespice'
	)
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.html.formfields.multiselect'] = array(
	'scripts' => array(
		'bluespice/bluespice.html.formfields.multiselect.js'
	),
	'dependencies' => array(
		'ext.bluespice'
	)
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.compat.vector.styles'] = array(
	'styles' => array(
		'bluespice.compat/bluespice.compat.vector.fixes.css'
	),
	'position' => 'top'
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.testsystem'] = array(
	'scripts'=>'bluespice/bluespice.testsystem.js'
) + $aResourceModuleTemplate;

unset( $aResourceModuleTemplate );
