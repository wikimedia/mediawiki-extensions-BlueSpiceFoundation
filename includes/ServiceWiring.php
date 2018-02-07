<?php

use MediaWiki\MediaWikiServices;
use BlueSpice\ExtensionAttributeBasedRegistry;

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
		return new \BlueSpice\ConfigDefinitionFactory(
			$services->getConfigFactory()->makeConfig( 'bsg' )
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

	'BSEntityRegistry' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\EntityRegistry(
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSEntityConfigFactory' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\EntityConfigFactory(
			$services->getService( 'BSEntityRegistry' ),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSEntityFactory' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\EntityFactory(
			$services->getService( 'BSEntityRegistry' ),
			$services->getService( 'BSEntityConfigFactory' ),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'BSAdminToolFactory' => function ( MediaWikiServices $services ) {
		$attribute = \ExtensionRegistry::getInstance()->getAttribute( 'BlueSpiceFoundationAdminToolRegistry' );
		return new \BlueSpice\AdminToolFactory( $attribute );
	},

	'BSTagFactory' => function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry( 'BlueSpiceFoundationTagRegistry' );
		return new \BlueSpice\TagFactory( $registry );
	},

	'BSRoleManager' => function ( MediaWikiServices $services ) {
		return \BlueSpice\Permission\Role\Manager::getInstance(
				$GLOBALS[ 'wgGroupPermissions' ],
				$GLOBALS[ 'bsgGroupRoles' ],
				$GLOBALS[ 'bsgEnableRoleSystem' ]
		);
	},

	'BSPermissionRegistry' => function( MediaWikiServices $services ) {
		global $bsgPermissionConfigDefault, $bsgPermissionConfig;
		return \BlueSpice\Permission\Registry::getInstance(
			$GLOBALS[ 'bsgPermissionConfigDefault' ],
			$GLOBALS[ 'bsgPermissionConfig' ]
		);
	}
];
