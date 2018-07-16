<?php

/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @abstract
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Sebastian Ulbricht <sebastian.ulbricht@dragon-design.hk>
 * @author Robert Vogel <vogel@hallowelt.com>
 * @author Stephan Muggli <muggli@hallowelt.com>
 */
// Last Review: MRG20100813
use MediaWiki\MediaWikiServices;
use BlueSpice\Extension;

/**
 * @deprecated since version 3.0.0
 */
class BsExtensionManager {

	/**
	 * @deprecated since version 3.0.0 - Use Service
	 * ('BSExtensionRegistry')->getExtensionDefinitions instead
	 * @return array
	 */
	public static function getRegisteredExtensions() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$registry = MediaWikiServices::getInstance()->getService(
			'BSExtensionRegistry'
		);
		return $registry->getExtensionDefinitions();
	}

	/**
	 * @deprecated since version 3.0.0 - Use Service
	 * ('BSExtensionFactory')->getExtensions instead
	 * @return Extension[]
	 */
	public static function getRunningExtensions() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$factory = MediaWikiServices::getInstance()->getService(
			'BSExtensionFactory'
		);
		return $factory->getExtensions();
	}

	/**
	 * Returns an instance of the requested BlueSpice extension or null, when
	 * not found / not active
	 * @deprecated since version 3.0.0 - Use Service
	 * ('BSExtensionFactory')->getExtension instead
	 * @param string $name
	 * @return Extension
	 */
	public static function getExtension( $name ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$factory = MediaWikiServices::getInstance()->getService(
			'BSExtensionFactory'
		);
		$extension = $factory->getExtension( $name );
		if( !$extension ) {
			//Backwards compatibility: extensions will have a BlueSice prefix in
			//the future
			$extension = $factory->getExtension( "BlueSpice$name" );
		}
		return $extension;
	}

	/**
	 * Returns a list of all running BlueSpice extensions
	 * @deprecated since version 3.0.0 - Use Service
	 * ('BSExtensionRegistry')->getNames instead
	 * @return array
	 */
	public static function getExtensionNames() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$registry = MediaWikiServices::getInstance()->getService(
			'BSExtensionRegistry'
		);
		return $registry->getNames();
	}

	/**
	 * Provides an array of inforation sets about all registered extensions
	 * @deprecated since version 3.0.0 - Use Service
	 * ('BSExtensionFactory')->getExtensionInformation instead
	 * @return array
	 */
	public static function getExtensionInformation() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$factory = MediaWikiServices::getInstance()->getService(
			'BSExtensionFactory'
		);
		return $factory->getExtensionInformation();
	}
}
