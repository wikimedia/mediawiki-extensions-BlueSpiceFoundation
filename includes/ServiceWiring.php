<?php

use BlueSpice\DeferredNotificationStack;
use BlueSpice\ExtensionAttributeBasedRegistry;
use MediaWiki\Context\RequestContext;
use MediaWiki\MediaWikiServices;
use MediaWiki\Registration\ExtensionRegistry;

return [

	'BSExtensionRegistry' => static function ( MediaWikiServices $services ) {
		return new \BlueSpice\ExtensionRegistry(
			ExtensionRegistry::getInstance(),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSExtensionFactory' => static function ( MediaWikiServices $services ) {
		return new \BlueSpice\ExtensionFactory(
			$services->getService( 'BSExtensionRegistry' ),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSConfigDefinitionFactory' => static function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationConfigDefinitionRegistry'
		);
		return new \BlueSpice\ConfigDefinitionFactory(
			$services->getConfigFactory()->makeConfig( 'bsg' ),
			$registry
		);
	},

	'BSEntityConfigFactory' => static function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationEntityRegistry'
		);
		return new \BlueSpice\EntityConfigFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSEntityFactory' => static function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationEntityRegistry'
		);
		return new \BlueSpice\EntityFactory(
			$registry,
			$services->getService( 'BSEntityConfigFactory' ),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSAdminToolFactory' => static function ( MediaWikiServices $services ) {
		$attribute = ExtensionRegistry::getInstance()->getAttribute(
			'BlueSpiceFoundationAdminToolRegistry'
		);
		return new \BlueSpice\AdminToolFactory( $attribute );
	},

	'BSTagFactory' => static function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry( 'BlueSpiceFoundationTagRegistry' );
		return new \BlueSpice\TagFactory( $registry );
	},

	'BSRoleFactory' => static function ( MediaWikiServices $services ) {
		$roles = ExtensionRegistry::getInstance()->getAttribute(
			'BlueSpiceFoundationRoleRegistry'
		);
		return new \BlueSpice\Permission\RoleFactory(
			$roles,
			$services->getService( 'BSPermissionRegistry' )
		);
	},

	'BSRoleManager' => static function ( MediaWikiServices $services ) {
		$roles = ExtensionRegistry::getInstance()->getAttribute( 'BlueSpiceFoundationRoles' );
		return new \BlueSpice\Permission\RoleManager(
			$GLOBALS[ 'wgGroupPermissions' ],
			$GLOBALS[ 'bsgGroupRoles' ],
			$GLOBALS[ 'bsgEnableRoleSystem' ],
			$roles,
			$services->getService( 'BSPermissionRegistry' ),
			$services->getService( 'BSRoleFactory' )
		);
	},

	'BSPermissionRegistry' => static function ( MediaWikiServices $services ) {
		return \BlueSpice\Permission\PermissionRegistry::getInstance(
			$GLOBALS[ 'bsgPermissionConfigDefault' ],
			$GLOBALS[ 'bsgPermissionConfig' ]
		);
	},

	'BSPermissionLockdownFactory' => static function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationPermissionLockdownRegistry'
		);
		return new \BlueSpice\PermissionLockdownFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' ),
			RequestContext::getMain()
		);
	},

	'BSRendererFactory' => static function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationRendererRegistry'
		);

		return new \BlueSpice\RendererFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSSkinDataRendererFactory' => static function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationSkinDataRendererRegistry'
		);

		return new \BlueSpice\SkinDataRendererFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSSettingPathFactory' => static function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationSettingPathRegistry'
		);

		return new \BlueSpice\SettingPathFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSTaskFactory' => static function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationTaskRegistry'
		);

		return new \BlueSpice\TaskFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSUtilityFactory' => static function ( MediaWikiServices $services ) {
		return new \BlueSpice\UtilityFactory(
			$services
		);
	},

	'BSTargetCacheFactory' => static function ( MediaWikiServices $services ) {
		$registry = new \BlueSpice\ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationTargetCacheRegistry'
		);

		return new \BlueSpice\TargetCacheFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' ),
			$services->getService( 'BSUtilityFactory' )->getCacheHelper()
		);
	},

	'BSTargetCacheTitle' => static function ( MediaWikiServices $services ) {
		return $services->getService( 'BSTargetCacheFactory' )->get( 'title' );
	},

	'BSTemplateFactory' => static function ( MediaWikiServices $services ) {
		$registry = new \BlueSpice\ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationTemplateHanderRegistry'
		);
		return new \BlueSpice\TemplateFactory(
			$registry,
			$services->getService( 'BSUtilityFactory' )->getTemplateHelper()
		);
	},

	'BSDeferredNotificationStack' => static function ( MediaWikiServices $services ) {
		$request = RequestContext::getMain()->getRequest();
		return new DeferredNotificationStack( $request );
	},

	'BSPageHeaderBeforeContentFactory' => static function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
				'BlueSpiceFoundationPageHeaderBeforeContentRegistry'
			);
		$context = RequestContext::getMain();
		$config = $services->getConfigFactory()->makeConfig( 'bsg' );

		return new \BlueSpice\PageHeaderBeforeContentFactory( $registry, $context, $config );
	},

	'BSSecondaryDataUpdater' => static function ( MediaWikiServices $services ) {
		/**
		 * DEPRECATED
		 * @deprecated since version 4.3 - use native mediawiki functionality
		 */
		wfDebugLog( 'bluespice-deprecations', 'BSSecondaryDataUpdater', 'private' );
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationSecondaryDataUpdateRegistry'
		);
		return new \BlueSpice\SecondaryDataUpdater( $registry );
	},
];
