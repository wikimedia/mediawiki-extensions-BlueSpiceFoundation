<?php

/**
 * This file is part of blue spice for MediaWiki.
 *
 * @abstract
 * @copyright Copyright (c) 2010, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Sebastian Ulbricht, Robert Vogel
 * @version 0.1.0 beta
 *
 * $LastChangedDate: 2013-06-19 15:20:31 +0200 (Mi, 19 Jun 2013) $
 * $LastChangedBy: rvogel $
 * $Rev: 9812 $
 * $Id: ExtensionManager.class.php 9812 2013-06-19 13:20:31Z rvogel $
 */
// Last Review: MRG20100813

class BsExtensionManager {

	protected static $prRegisteredExtensions = array( );
	protected static $prRunlevelRegister = array( );
	protected static $prRunningExtensions = array( );
	protected static $aContexts = array( );
	protected static $aActiveContexts = array( );
	protected static $aIncludedClasses = array( );

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

	public static function addScriptFileToContext ( $sKey, $sGroup, $sPath, $sFile, $iOptions = 0, $bLanguage = false ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		$sKey = self::addContext( $sKey );
		self::$aContexts[ $sKey ][ 'scripts' ][ ] = array( $sGroup, $sPath, $sFile, $iOptions, $bLanguage );
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	public static function addStyleSheetToContext ( $sKey, $sGroup, $sFile, $iOptions = 0 ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		$sKey = self::addContext( $sKey );
		self::$aContexts[ $sKey ][ 'styles' ][ ] = array( $sGroup, $sFile, $iOptions );
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	public static function loadAllScriptFiles () {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		foreach ( self::$aActiveContexts as $sKey => $bActive ) {
			if ( $bActive && BsScriptManager::needsExtJs( $sKey ) ) {
				BsCore::loadExtJs();
			}
		}
		global $wgLang;
		$sLanguageCode = $wgLang->getCode();
		foreach ( self::$aActiveContexts as $sKey => $bActive ) {
			if ( $bActive && array_key_exists( $sKey, self::$aContexts ) ) {
				foreach ( self::$aContexts[ $sKey ][ 'scripts' ] as $aScript ) {
					BsScriptManager::add( $aScript[ 0 ], $aScript[ 1 ] . '/' . $aScript[ 2 ] . '.js', $aScript[ 3 ] );
					if ( $aScript[ 4 ] && $sLanguageCode != 'en' ) {
						BsScriptManager::add( $aScript[ 0 ], $aScript[ 1 ] . '/i18n/' . $aScript[ 2 ] . '.' . $sLanguageCode . '.js', $aScript[ 3 ] );
					}
				}
			}
		}
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	public static function loadAllStyleSheets () {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		foreach ( self::$aActiveContexts as $sKey => $bActive ) {
			if ( $bActive && array_key_exists( $sKey, self::$aContexts ) ) {
				foreach ( self::$aContexts[ $sKey ][ 'styles' ] as $aStyle ) {
					BsStyleManager::add( $aStyle[ 0 ], $aStyle[ 1 ], $aStyle[ 2 ] );
				}
			}
		}
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	public static function registerExtension ( $name, $runlevel = BsRUNLEVEL::FULL, $action = BsACTION::NONE, $baseDir = 'ext' ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		self::$prRegisteredExtensions[ 0 ][ $name ] = array( 'runlevel' => $runlevel,
			'action' => $action,
			'baseDir' => $baseDir );
		self::$prRunlevelRegister[ $runlevel ][ $name ] = & self::$prRegisteredExtensions[ 0 ][ $name ];
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	public static function includeExtentionFiles ( $oAdapter ) {
		global $IP;
		wfProfileIn( 'Performance: ' . __METHOD__ );

		$sAction = BsCore::getParam( 'action', 'view' );
		switch ( $sAction ) {
			case 'remote':
				$runlevel = BsRUNLEVEL::REMOTE;
				break;
			default:
				$runlevel = BsRUNLEVEL::FULL;
				break;
		}

		$path = "$IP/extensions/BlueSpiceExtensions";
		$registers = array( );
		if ( $runlevel ) {
			foreach ( self::$prRunlevelRegister as $_runlevel => $_register ) {
				if ( $runlevel & $_runlevel ) {
					$registers[ ] = & self::$prRunlevelRegister[ $_runlevel ];
				}
			}
		} else {
			// TODO MRG20100813: Was passiert hier?
			$registers[ ] = self::$prRegisteredExtensions[ 0 ];
		}
		foreach ( $registers as $register ) {
			foreach ( $register as $name => $attributes ) {
				if( $attributes['baseDir'] != 'ext' ) {
					require($attributes['baseDir'] . DS . $name . '.class.php');
				} else {
					require($path . DS . $name . DS . $name . '.class.php');
				}
				if ( BsACTION::LOAD_SPECIALPAGE|BsACTION::LOAD_ON_API & $attributes[ 'action' ] ) {
					self::$aIncludedClasses[ $name ] = true;
				} else {
					self::$aIncludedClasses[ $name ] = false;
				}
			}
		}

		global $wgCommandLineMode;
		$bRunOnSpecialPage = ( bool ) BsAdapterMW::isSpecial();
		if ( $bRunOnSpecialPage || $wgCommandLineMode ) {
			foreach ( self::$aIncludedClasses as $name => $early_load ) {
				if ( class_exists( 'Bs' . $name, false ) ) {
					$class = 'Bs' . $name;
				} else {
					$class = $name;
				}
				self::$prRunningExtensions[ $name ] = new $class();
				self::$prRunningExtensions[ $name ]->setAdapter( $oAdapter );
				self::$prRunningExtensions[ $name ]->setup();
			}
		} else {
			foreach ( self::$aIncludedClasses as $name => $early_load ) {
				if ( $early_load ) {
					if ( class_exists( 'Bs' . $name, false ) ) {
						$class = 'Bs' . $name;
					} else {
						$class = $name;
					}
					self::$prRunningExtensions[ $name ] = new $class();
					self::$prRunningExtensions[ $name ]->setAdapter( $oAdapter );
					self::$prRunningExtensions[ $name ]->setup();
				}
			}
		}

		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	public static function loadExtensions ( $runlevel = BsRUNLEVEL::FULL, $adapter = NULL ) {
		global $wgCommandLineMode;
		$bRunOnSpecialPage = ( bool ) BsAdapterMW::isSpecial();
		if ( $bRunOnSpecialPage || $wgCommandLineMode ) {
			return;
		}
		wfProfileIn( 'Performance: ' . __METHOD__ );
		foreach ( self::$aIncludedClasses as $name => $early_load ) {
			if ( $early_load ) {
				continue;
			}
			if ( class_exists( 'Bs' . $name, false ) ) {
				$class = 'Bs' . $name;
			} else {
				$class = $name;
			}
			self::$prRunningExtensions[ $name ] = new $class();
			self::$prRunningExtensions[ $name ]->setAdapter( $adapter );
			self::$prRunningExtensions[ $name ]->setup();
		}
		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	// TODO MRG20100813: Brauchen wir das & ?
	public static function &getExtension ( $name ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		$oExtension = null;
		if( isset(self::$prRunningExtensions[ $name ] ) )
			$oExtension = self::$prRunningExtensions[ $name ];
		wfProfileOut( 'Performance: ' . __METHOD__ );
		return $oExtension;
	}
	
	public static function getExtensionNames() {
		return array_keys(self::$prRegisteredExtensions[0]);
	}

	// TODO MRG20100813: Bitte in getExtensionInformation umbenennen
	public static function getExtensionInformations () {
		wfProfileIn( 'Performance: ' . __METHOD__ );
		$informations = array( );
		foreach ( self::$prRegisteredExtensions[ 0 ] as $name => $attributes ) {
			if ( !class_exists( $name, false ) && !class_exists( 'Bs' . $name, false ) ) {
				if( isset($attributes[ 'baseDir' ]) ) {
					require($attributes[ 'baseDir' ] . DS . $name . DS . $name . '.class.php');
				}
				else {
					require(BsConfig::get( 'CORE::BlueSpiceAdapterPath' ) . DS . $name . DS . $name . '.class.php');
				}
			}
			if ( class_exists( 'Bs' . $name, false ) ) {
				$class = 'Bs' . $name;
			} else {
				$class = $name;
			}
			$tmp = new $class();
			$informations[ $name ] = $tmp->getInfo();
		}
		wfProfileOut( 'Performance: ' . __METHOD__ );
		return $informations;
	}

}