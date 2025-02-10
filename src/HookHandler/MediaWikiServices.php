<?php

namespace BlueSpice\HookHandler;

use BlueSpice\Http\HttpRequestFactory;
use MediaWiki\Config\GlobalVarConfig;
use MediaWiki\Hook\MediaWikiServicesHook;
use MediaWiki\MediaWikiServices as MWMediaWikiServices;

class MediaWikiServices implements MediaWikiServicesHook {

	/**
	 *
	 * @param MWMediaWikiServices $services
	 * @return bool
	 */
	public function onMediaWikiServices( $services ) {
		$services->addServiceManipulator(
			'HttpRequestFactory',
			static function ( $originalFactory ) {
				// Unfortunately we can not use the ConfigFactory service here, as the
				// service container is still disabled at this time.
				// Therefore we fall back to old-fashioned `GlobalVarConfig`.
				$bsgConfig = new GlobalVarConfig( 'bsg' );
				$defaultOptions = $bsgConfig->get( 'HttpRequestDefaultOptions' );
				return new HttpRequestFactory( $originalFactory, $defaultOptions );
			}
		);
		return true;
	}
}
