<?php

/**
 * This file is part of BlueSpice MediaWiki.
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

	protected static function makeExtensionDefinition( $sExtName = "", $aDefinition = array() ) {
		global $bsgBlueSpiceExtInfo;
		$aExtensions = ExtensionRegistry::getInstance()->getAllThings();

		//Some BlueSpice extensions have been registered wrong in the past.
		//The the extension name used as key in bsgExtensions must be equal with
		//the extensions name in the "name" attribute of the extension.json!
		$aExtension = null;
		if( isset( $aExtensions[$sExtName] ) ) {
			$aExtension = $aExtensions[$sExtName];
		} elseif ( isset( $aExtensions["BlueSpice$sExtName"] ) ) {
			$aExtension = $aExtensions["BlueSpice$sExtName"];
		} else {
			$sFixedExtName = str_replace( 'BlueSpice', '', $sExtName );
			if( isset( $aExtensions[$sFixedExtName] ) ) {
				$aExtension = $aExtensions[$sFixedExtName];
			}
		}
		if( !$aExtension ) {
			throw new BsException(
				"$sExtName is not a registered extension!"
			);
		}

		$aDefinition = array_merge(
			$aExtension,
			$aDefinition
		);
		if( !isset( $aDefinition['className'] ) ) {
			$aDefinition['className'] = $sExtName;
		}
		if( !isset( $aDefinition['extPath'] ) ) {
			$aDefinition['extPath'] = "";
		}
		if( !isset( $aDefinition['status'] ) ) {
			$aDefinition['status'] = "default";
		}
		if( !isset( $aDefinition['package'] ) ) {
			$aDefinition['package'] = "default";
		}
		$aDefinition['status'] = str_replace(
			'default',
			$GLOBALS['bsgBlueSpiceExtInfo']['status'],
			$aDefinition['status']
		);
		$aDefinition['package'] = str_replace(
			'default',
			$GLOBALS['bsgBlueSpiceExtInfo']['package'],
			$aDefinition['package']
		);
		return $aDefinition;
	}

	/**
	 * Collects and initializes all BlueSpice extensions
	 * @param BsCore $oCore
	 * @throws BsException
	 */
	public static function initialiseExtensions( $oCore ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		$aBSExtFromJSON = ExtensionRegistry::getInstance()->getAttribute(
			'bsgExtensions'
		);

		if( !empty( $aBSExtFromJSON ) ) {
			$GLOBALS['bsgExtensions'] = array_replace_recursive(
				$aBSExtFromJSON,
				$GLOBALS['bsgExtensions']
			);
		}

		foreach( $GLOBALS['bsgExtensions'] as $sExtName => $aDefinition ) {
			//Skip the definitions for BlueSpiceFoundation, as it is not a
			//extension we can instantiate
			if( $sExtName === 'BlueSpiceFoundation' ) {
				continue;
			}
			self::$prRegisteredExtensions[$sExtName]
				= self::makeExtensionDefinition( $sExtName, $aDefinition );
		}

		foreach ( self::$prRegisteredExtensions as $sExtName => $aDefinition ) {
			$sClassName = $aDefinition['className'];

			if( !class_exists( $sClassName ) ) {
				throw new BsException(
					"Class $sClassName for Extension $sExtName not found!"
				);
			}

			$config = \MediaWiki\MediaWikiServices::getInstance()
				->getConfigFactory()->makeConfig( 'bsg' );
			self::$prRunningExtensions[$sExtName] = new $sClassName(
				$aDefinition,
				RequestContext::getMain(),
				$config
			);
			if( !self::$prRunningExtensions[$sExtName] instanceof \BsExtensionMW ) {
				wfProfileOut( 'Performance: ' . __METHOD__ );
				return;
			}

			//this is for extensions using the old mechanism and may have their
			//own __constructor
			self::$prRunningExtensions[$sExtName]->setConfig( $config );
			self::$prRunningExtensions[$sExtName]->setContext(
				RequestContext::getMain()
			);
			self::$prRunningExtensions[$sExtName]->setCore( $oCore );
			self::$prRunningExtensions[$sExtName]->setup(
				$sExtName,
				$aDefinition
			);
		}

		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	/**
	 * Returns an instance of the requested BlueSpice extension or null, when
	 * not found / not active
	 * @param string $name
	 * @return BsExtensionMW
	 */
	public static function getExtension( $name ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		//Backwards compatibility: extensions will have a BlueSice prefix in
		//the future
		$aExtensions = self::getRunningExtensions();
		if ( isset( $aExtensions["BlueSpice$name"] ) ) {
			//TODO: Add a wfDeprecated( __METHOD__, 'next BS Version' );
			wfProfileOut( 'Performance: ' . __METHOD__ );
			return $aExtensions["BlueSpice$name"];
		}
		if ( isset( $aExtensions[$name] ) ) {
			wfProfileOut( 'Performance: ' . __METHOD__ );
			return $aExtensions[$name];
		}
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	/**
	 * Returns a list of all running BlueSpice extensions
	 * @return array
	 */
	public static function getExtensionNames() {
		return array_keys( self::getRunningExtensions() );
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

	/**
	 * Hook handler for ResourceLoaderGetConfigVars - Appends the BlueSpice
	 * version number to JS config vars
	 * @global array $bsgBlueSpiceExtInfo
	 * @param array $vars
	 * @return boolean
	 */
	public static function onResourceLoaderGetConfigVars( array &$vars ) {
		global $bsgBlueSpiceExtInfo;

		$vars["bsgVersion"] = $bsgBlueSpiceExtInfo["version"];

		return true;
	}
}