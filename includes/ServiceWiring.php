<?php

use MediaWiki\MediaWikiServices;

return [

	'DynamicFileDispatcherFactory' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\DynamicFileDispatcher\Factory(
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'DynamicFileDispatcherUrlBuilder' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\DynamicFileDispatcher\UrlBuilder(
			$services->getService( 'DynamicFileDispatcherFactory' ),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'EntityRegistry' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\EntityRegistry(
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'EntityConfigFactory' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\EntityConfigFactory(
			$services->getService( 'EntityRegistry' ),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

	'EntityFactory' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\EntityFactory(
			$services->getService( 'EntityRegistry' ),
			$services->getService( 'EntityConfigFactory' ),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},
];
