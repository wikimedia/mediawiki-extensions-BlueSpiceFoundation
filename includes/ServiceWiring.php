<?php

use BlueSpice\DeferredNotificationStack;
use BlueSpice\ExtensionAttributeBasedRegistry;
use MediaWiki\MediaWikiServices;

return [

	'BSExtensionRegistry' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\ExtensionRegistry(
			\ExtensionRegistry::getInstance(),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSExtensionFactory' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\ExtensionFactory(
			$services->getService( 'BSExtensionRegistry' ),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSConfigDefinitionFactory' => function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationConfigDefinitionRegistry'
		);
		return new \BlueSpice\ConfigDefinitionFactory(
			$services->getConfigFactory()->makeConfig( 'bsg' ),
			$registry
		);
	},

	'BSDynamicFileDispatcherFactory' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\DynamicFileDispatcher\Factory(
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSDynamicFileDispatcherUrlBuilder' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\DynamicFileDispatcher\UrlBuilder(
			$services->getService( 'BSDynamicFileDispatcherFactory' ),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSEntityConfigFactory' => function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationEntityRegistry'
		);
		return new \BlueSpice\EntityConfigFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSEntityFactory' => function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationEntityRegistry'
		);
		return new \BlueSpice\EntityFactory(
			$registry,
			$services->getService( 'BSEntityConfigFactory' ),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSAdminToolFactory' => function ( MediaWikiServices $services ) {
		$attribute = \ExtensionRegistry::getInstance()->getAttribute(
			'BlueSpiceFoundationAdminToolRegistry'
		);
		return new \BlueSpice\AdminToolFactory( $attribute );
	},

	'BSTagFactory' => function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry( 'BlueSpiceFoundationTagRegistry' );
		return new \BlueSpice\TagFactory( $registry );
	},

	'BSRoleFactory' => function ( MediaWikiServices $services ) {
		$roles = \ExtensionRegistry::getInstance()->getAttribute(
			'BlueSpiceFoundationRoleRegistry'
		);
		return new \BlueSpice\Permission\RoleFactory(
			$roles,
			$services->getService( 'BSPermissionRegistry' )
		);
	},

	'BSRoleManager' => function ( MediaWikiServices $services ) {
		$roles = \ExtensionRegistry::getInstance()->getAttribute( 'BlueSpiceFoundationRoles' );
		return new \BlueSpice\Permission\RoleManager(
			$GLOBALS[ 'wgGroupPermissions' ],
			$GLOBALS[ 'bsgGroupRoles' ],
			$GLOBALS[ 'bsgEnableRoleSystem' ],
			$roles,
			$services->getService( 'BSPermissionRegistry' ),
			$services->getService( 'BSRoleFactory' )
		);
	},

	'BSPermissionRegistry' => function ( MediaWikiServices $services ) {
		return \BlueSpice\Permission\PermissionRegistry::getInstance(
			$GLOBALS[ 'bsgPermissionConfigDefault' ],
			$GLOBALS[ 'bsgPermissionConfig' ]
		);
	},

	'BSPermissionLockdownFactory' => function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationPermissionLockdownRegistry'
		);
		return new \BlueSpice\PermissionLockdownFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' ),
			\RequestContext::getMain()
		);
	},

	'BSRendererFactory' => function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationRendererRegistry'
		);

		return new \BlueSpice\RendererFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSSkinDataRendererFactory' => function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationSkinDataRendererRegistry'
		);

		return new \BlueSpice\SkinDataRendererFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSSettingPathFactory' => function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationSettingPathRegistry'
		);

		return new \BlueSpice\SettingPathFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSTaskFactory' => function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationTaskRegistry'
		);

		return new \BlueSpice\TaskFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSUtilityFactory' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\UtilityFactory(
			$services
		);
	},

	'BSNotificationManager' => function ( MediaWikiServices $services ) {
		$regFuncRegistry = new \BlueSpice\ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationNotificationRegistrationFunctions'
		);

		return new \BlueSpice\NotificationManager(
			$regFuncRegistry,
			$services->getService( 'MWStakeNotificationsNotifier' )
		);
	},

	'BSTargetCacheFactory' => function ( MediaWikiServices $services ) {
		$registry = new \BlueSpice\ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationTargetCacheRegistry'
		);

		return new \BlueSpice\TargetCacheFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' ),
			$services->getService( 'BSUtilityFactory' )->getCacheHelper()
		);
	},

	'BSTargetCacheTitle' => function ( MediaWikiServices $services ) {
		return $services->getService( 'BSTargetCacheFactory' )->get( 'title' );
	},

	'BSTemplateFactory' => function ( MediaWikiServices $services ) {
		$registry = new \BlueSpice\ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationTemplateHanderRegistry'
		);
		return new \BlueSpice\TemplateFactory(
			$registry,
			$services->getService( 'BSUtilityFactory' )->getTemplateHelper()
		);
	},

	'BSPageInfoElementFactory' => function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
				'BlueSpiceFoundationPageInfoElementRegistry'
			);
		$context = \RequestContext::getMain();
		$config = $services->getConfigFactory()->makeConfig( 'bsg' );

		return new \BlueSpice\PageInfoElementFactory( $registry, $context, $config );
	},

	'BSDeferredNotificationStack' => function ( MediaWikiServices $services ) {
		$request = \RequestContext::getMain()->getRequest();
		return new DeferredNotificationStack( $request );
	},

	'BSPageHeaderBeforeContentFactory' => function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
				'BlueSpiceFoundationPageHeaderBeforeContentRegistry'
			);
		$context = \RequestContext::getMain();
		$config = $services->getConfigFactory()->makeConfig( 'bsg' );

		return new \BlueSpice\PageHeaderBeforeContentFactory( $registry, $context, $config );
	},
];
