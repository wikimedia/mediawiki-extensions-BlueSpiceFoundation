<?php

/**
 * This file is part of blue spice for MediaWiki.
 *
 * @abstract
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Sebastian Ulbricht <sebastian.ulbricht@dragon-design.hk>
 * @author Robert Vogel <vogel@hallowelt.com>
 * @author Stephan Muggli <muggli@hallowelt.com>
 * @version 2.22.0
 */
// Last Review: MRG20100813

class BsExtensionManager {

	protected static $prRegisteredExtensions = array();
	/**
	 *
	 * @var BsExtensionMW[]
	 */
	protected static $prRunningExtensions = array();
	protected static $aContexts = array();
	protected static $aActiveContexts = array();

	/**
	 * @deprecated since version 2.27.0
	 * @param string $sKey Context key
	 * @return string normalized key
	 */
	public static function addContext( $sKey ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		wfDeprecated( __METHOD__, '2.27.0' );
		$sKey = strtoupper( $sKey );
		if ( !array_key_exists( $sKey, self::$aContexts ) ) {
			self::$aContexts[ $sKey ] = array(
				'scripts' => array( ),
				'styles' => array( )
			);
		}
		wfProfileOut( 'Performance: ' . __METHOD__ );
		return $sKey;
	}

	/**
	 * @deprecated since version 2.27.0
	 * @param string $sKey Context key
	 */
	public static function setContext( $sKey ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		wfDeprecated( __METHOD__, '2.27.0' );
		$sKey = self::addContext( $sKey );
		if ( !array_key_exists( $sKey, self::$aActiveContexts ) ) {
			self::$aActiveContexts[ $sKey ] = true;
		}
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	/**
	 * @deprecated since version 2.27.0
	 * @param string $sKey Context key
	 * @return bool
	 */
	public static function isContextActive( $sKey ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		wfDeprecated( __METHOD__, '2.27.0' );
		$bResult = array_key_exists( strtoupper( $sKey ), self::$aActiveContexts );
		wfProfileOut( 'Performance: ' . __METHOD__ );
		return $bResult;
	}

	/**
	 * @deprecated since version 2.27.0
	 * @param string $sKey Context key
	 */
	public static function removeContext( $sKey ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		wfDeprecated( __METHOD__, '2.27.0' );
		$sKey = strtoupper( $sKey );
		if ( array_key_exists( $sKey, self::$aContexts ) ) {
			unset( self::$aContexts[ $sKey ] );
			self::$aActiveContexts[ $sKey ] = false;
		}
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	/**
	 * DEPRECATED: Use global $bsgExtensions instead.
	 * Define $bsgExtensions in extension.json
	 * "bsgExtensions": {
	 *    "ExtName": {
	 *        "className": "ExtClass",
	 *        "extPath": "/PackagePath/ExtDir",
	 *        "status" => "stable", //optional
	 *        "package" => "BlueSpice free", //optional
	 *     }
	 * },
	 * @deprecated since version 2.27.0
	 * @param string $name
	 * @param integer $runlevel
	 * @param integer $action
	 * @param string $extPath
	 */
	public static function registerExtension( $name, $runlevel = BsRUNLEVEL::FULL, $action = BsACTION::NONE, $extPath = 'ext' ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		wfDeprecated( __METHOD__, '2.27.0' );

		if( empty( $extPath ) || $extPath == 'ext' ) {
			$extPath = "/BlueSpiceExtensions/$name";
		}
		//HACKY: Old dir 2 ext path calculations can finally be removed with
		//this method, yay!
		global $IP;
		$extPath = str_replace( '\\', '/', $extPath );
		$sIPPath = str_replace( '\\', '/', $IP );
		$extPath = str_replace( "$sIPPath/extensions", '', $extPath );

		//Hacky, but the Preferences extension has the prefix Bs to not
		//having the same name as the MW class.
		$sClassName = $name;
		if( $name == 'Preferences' ) {
			$sClassName = "Bs$sClassName";
		}

		$GLOBALS['bsgExtensions'][$name] = array(
			'className' => $sClassName,
			'extPath' => $extPath,
			'deprecatedSince' => '2.27.0',
		);
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	/**
	 *
	 * @return array
	 */
	public static function getRegisteredExtensions() {
		return self::$prRegisteredExtensions;
	}

	/**
	 *
	 * @return BsExtensionMW[]
	 */
	public static function getRunningExtensions() {
		return self::$prRunningExtensions;
	}

	protected static function makeExtensionConfig( $sExtName = "", $aConfig = array() ) {
		global $bsgBlueSpiceExtInfo;
		if( !isset( $aConfig['className'] ) ) {
			$aConfig['className'] = $sExtName;
		}
		if( !isset( $aConfig['extPath'] ) ) {
			$aConfig['extPath'] = "";
		}
		if( !isset( $aConfig['status'] ) ) {
			$aConfig['status'] = "default";
		}
		if( !isset( $aConfig['package'] ) ) {
			$aConfig['package'] = "default";
		}
		$aConfig['status'] = str_replace(
			'default',
			$bsgBlueSpiceExtInfo['status'],
			$aConfig['status']
		);
		$aConfig['package'] = str_replace(
			'default',
			$bsgBlueSpiceExtInfo['package'],
			$aConfig['package']
		);
		return $aConfig;
	}

	public static function initialiseExtensions( $oCore ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		$aBSExtFromJSON = ExtensionRegistry::getInstance()->getAttribute(
			'bsgExtensions'
		);
		if( !empty( $aBSExtFromJSON ) ) {
			$GLOBALS['bsgExtensions'] = array_merge_recursive(
				$GLOBALS['bsgExtensions'],
				$aBSExtFromJSON
			);
		}

		foreach( $GLOBALS['bsgExtensions'] as $sExtName => $aConfig ) {
			self::$prRegisteredExtensions[$sExtName]
				= self::makeExtensionConfig( $sExtName, $aConfig );
		}

		foreach ( self::$prRegisteredExtensions as $sExtName => $aConfig ) {
			$sClassName = $aConfig['className'];

			if( !class_exists( $sClassName ) ) {
				throw new BsException(
					"Class $sClassName for Extension $sExtName not found!"
				);
			}

			self::$prRunningExtensions[$sExtName] = new $sClassName();
			self::$prRunningExtensions[$sExtName]->setCore( $oCore );
			self::$prRunningExtensions[$sExtName]->setup( $sExtName, $aConfig );
		}

		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	public static function getExtension( $name ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		//Backwards compatibility: extensions will have a BlueSice prefix in
		//the future
		if ( isset( self::$prRunningExtensions["BlueSpice$name"] ) ) {
			//TODO: Add a wfDeprecated( __METHOD__, 'next BS Version' );
			return self::$prRunningExtensions["BlueSpice$name"];
		}
		if ( isset( self::$prRunningExtensions[$name] ) ) {
			return self::$prRunningExtensions[$name];
		}
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	public static function getExtensionNames() {
		return array_keys( self::$prRegisteredExtensions );
	}

	/**
	 * Provides an array of inforation sets about all registered extensions
	 * @return array
	 */
	public static function getExtensionInformation() {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		$aInformation = array();
		foreach ( self::$prRunningExtensions as $sExtName => $oExt ) {
			$aInformation[$sExtName] = $oExt->getInfo();
		}

		wfProfileOut( 'Performance: ' . __METHOD__ );
		return $aInformation;
	}

	/**
	 * Creates a namespace AND a corresponding talk namespace with respect of
	 * an potential offest. You will need to register a
	 * "Extension.namespaces.php" file with "$wgExtensionMessagesFiles" as
	 * described on https://www.mediawiki.org/wiki/Localisation#Namespaces
	 *
	 * @deprecated since version 2.27.0
	 * @global array $wgExtraNamespaces
	 * @param string $sCanonicalName
	 * @param int $iBaseIndex
	 */
	public static function registerNamespace( $sCanonicalName, $iBaseIndex, $isSystemNamespace = true ) {
		wfDeprecated( __METHOD__, '2.27.0' );
		global $wgExtraNamespaces, $bsgSystemNamespaces;

		$sConstantName = 'NS_'.mb_strtoupper( $sCanonicalName );
		$iCalculatedNSId = BS_NS_OFFSET + $iBaseIndex;

		if ( !defined( $sConstantName ) ) {
			define( $sConstantName, $iCalculatedNSId );
			$wgExtraNamespaces[$iCalculatedNSId] = $sCanonicalName;
		}

		if ( $isSystemNamespace ) {
			$bsgSystemNamespaces[$iCalculatedNSId] = $sConstantName;
		}

		//Talk namespace
		$sConstantName .= '_TALK';
		$iCalculatedNSId++;

		if ( !defined( $sConstantName ) ) {
			define( $sConstantName, $iCalculatedNSId );
			$wgExtraNamespaces[$iCalculatedNSId] = $sCanonicalName.'_talk';
		}

		if ( $isSystemNamespace ) {
			$bsgSystemNamespaces[$iCalculatedNSId] = $sConstantName;
		}
	}
}