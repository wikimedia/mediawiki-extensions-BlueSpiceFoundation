<?php

/**
 * This file is part of blue spice for MediaWiki.
 *
 * @abstract
 * @copyright Copyright (c) 2010, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Sebastian Ulbricht <sebastian.ulbricht@dragon-design.hk>
 * @author Robert Vogel <vogel@hallowelt.biz>
 * @author Stephan Muggli <muggli@hallowelt.biz>
 * @version 2.22.0 
 */
// Last Review: MRG20100813

class BsExtensionManager {

	protected static $prRegisteredExtensions = array();
	protected static $prRunlevelRegister = array();
	protected static $prRunningExtensions = array();
	protected static $aContexts = array();
	protected static $aActiveContexts = array();
	protected static $aIncludedClasses = array();

	public static function addContext ( $sKey ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
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

	public static function setContext ( $sKey ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		$sKey = self::addContext( $sKey );
		if ( !array_key_exists( $sKey, self::$aActiveContexts ) ) {
			self::$aActiveContexts[ $sKey ] = true;
		}
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	public static function isContextActive ( $sKey ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		$bResult = array_key_exists( strtoupper( $sKey ), self::$aActiveContexts );
		wfProfileOut( 'Performance: ' . __METHOD__ );
		return $bResult;
	}

	public static function removeContext ( $sKey ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		$sKey = strtoupper( $sKey );
		if ( array_key_exists( $sKey, self::$aContexts ) ) {
			unset( self::$aContexts[ $sKey ] );
			self::$aActiveContexts[ $sKey ] = false;
		}
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	public static function registerExtension ( $name, $runlevel = BsRUNLEVEL::FULL, $action = BsACTION::NONE, $baseDir = 'ext' ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		self::$prRegisteredExtensions[ $name ] = array( 
			'runlevel' => $runlevel,
			'action'   => $action,
			'baseDir'  => $baseDir
		);
		self::$prRunlevelRegister[ $runlevel ][ $name ] = & self::$prRegisteredExtensions[ $name ];
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}
	
	public static function getRegisteredExtenions() {
		wfDeprecated( __METHOD__ );
		return self::$prRegisteredExtensions;
	}
	
	public static function getRegisteredExtensions() {
		return self::$prRegisteredExtensions;
	}

	public static function includeExtensionFiles ( $oCore ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		global $IP;

		$path = "$IP/extensions/BlueSpiceExtensions";

		foreach ( self::$prRunlevelRegister as $runlevel => $register ) {
			foreach ( $register as $name => $attributes ) {
				if ( $attributes['baseDir'] != 'ext' ) {
					require( $attributes['baseDir'] . DS . $name . '.class.php' );
				} else {
					require( $path . DS . $name . DS . $name . '.class.php' );
				}
				self::$aIncludedClasses[] = $name;

			}
		}

		foreach ( self::$aIncludedClasses as $key => $value ) {
			if ( class_exists( 'Bs' . $value, false ) ) {
				$class = 'Bs' . $value;
			} else {
				$class = $value;
			}
			self::$prRunningExtensions[ $value ] = new $class();
			self::$prRunningExtensions[ $value ]->setCore( $oCore );
			self::$prRunningExtensions[ $value ]->setup();
		}

		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	// TODO MRG20100813: Brauchen wir das & ?
	public static function &getExtension ( $name ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		$oExtension = null;
		if ( isset( self::$prRunningExtensions[ $name ] ) )
			$oExtension = self::$prRunningExtensions[ $name ];
		wfProfileOut( 'Performance: ' . __METHOD__ );
		return $oExtension;
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
		foreach ( self::$prRegisteredExtensions as $name => $attributes ) {
			if ( !class_exists( $name, false ) && !class_exists( 'Bs' . $name, false ) ) {
				if ( isset( $attributes[ 'baseDir' ] ) ) {
					require( $attributes[ 'baseDir' ] . DS . $name . DS . $name . '.class.php' );
				} else {
					require( BsConfig::get( 'MW::ScriptPath' ) . DS . 'extensions' . DS . 'BlueSpiceExtensions' . DS . $name . DS . $name . '.class.php' );
				}
			}
			if ( class_exists( 'Bs' . $name, false ) ) {
				$class = 'Bs' . $name;
			} else {
				$class = $name;
			}
			$tmp = new $class();
			$aInformation[ $name ] = $tmp->getInfo();
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
	 * @global array $wgExtraNamespaces
	 * @param string $sCanonicalName
	 * @param int $iBaseIndex
	 */
	public static function registerNamespace( $sCanonicalName, $iBaseIndex, $isSystemNamespace = true ) {
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