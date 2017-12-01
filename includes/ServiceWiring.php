<?php

use MediaWiki\MediaWikiServices;

return [

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
];
