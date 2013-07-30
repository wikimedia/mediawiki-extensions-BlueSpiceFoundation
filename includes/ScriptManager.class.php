<?php
/**
 * This file is part of BlueSpice for MediaWiki.
 *
 * @abstract
 * @copyright Copyright (c) 2007-2009, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Sebastian Ulbricht, Robert Vogel, Stephan Muggli
 * @version 1.20.2
 *
 * $LastChangedDate: 2013-06-13 10:32:52 +0200 (Do, 13 Jun 2013) $
 * $LastChangedBy: rvogel $
 * $Rev: 9719 $
 * $Id: ScriptManager.class.php 9719 2013-06-13 08:32:52Z rvogel $
 */
// Last review: MRG20100813

/**
 * the BsScriptManager
 * @package BlueSpice_Core
 * @subpackage Core
 */
class BsScriptManager {

	protected static $oFileManager = null;
	protected static $aClientScriptBlocks = array();
	protected static $aNeedsExtJs = array();

	public static function init() {
		self::$oFileManager = new BsFileManager();
	}

	// TODO MRG20100813: Was bedeutet 0? Kann man das durch eine Konstante ersezten?
	public static function add($group, $file, $options = 0) {
		self::$oFileManager->add($group, $file, $options);
	}

	public static function addNeedsExtJs( $sKey ) {
		self::$aNeedsExtJs[] = strtoupper( $sKey );
	}

	public static function needsExtJs( $sKey ) {
		return in_array( $sKey, self::$aNeedsExtJs );
	}

	public static function getOutput() { return '';
		wfProfileIn( 'BS::'.__METHOD__ );
		global $wgUser;

		// Necessary otherwise values are not correctly loaded
		BsConfig::loadSettings();
		BsConfig::loadUserSettings( $wgUser->getName() );

		$aScriptSettings = BsConfig::getScriptSettings();

		$sOut = "<script type=\"text/javascript\">\n";

		foreach ( $aScriptSettings as $oVar ) {
			$sValue = $oVar->getValue();
			if ( !( $oVar->getOptions() & BsConfig::TYPE_JSON ) ) {
				// @todo check for XSS
				if ( is_int( $sValue ) || is_float( $sValue )) {
					$sValue = "$sValue";
				}
				else if ( is_string( $sValue )) {
					$sValue = "'$sValue'";
				}
				else {
					$sValue = json_encode( $sValue );
				}
			}

			// All vars are outputed like this: var bsVisualEditorUse = true
			// VisualEditor = $oVar->getExtension()
			// Use = $oVar->getName()
			// true = $sValue
			$sOut .= "var bs{$oVar->getExtension()}{$oVar->getName()} = $sValue;\n";
		}

		$sOut .= "</script>\n";
		$sOut .= self::output();

		wfProfileOut( 'BS::'.__METHOD__ );
		return $sOut;
	}

	// TODO MRG20100813: Was bedeutet loaded, mini und output? woher kommen die?
	// TODO MRG20100813: Bitte hier wie auch bei anderen rekursiven funktionen anmerken, dass die
	// rekursiv verwendet werden.
	protected static function output() {
		wfProfileIn( 'BS::'.__METHOD__ );
		$aOutput = array();
		$aFileRegister = self::$oFileManager->getFileRegister();

		foreach( $aFileRegister as $sFile => $iOptions ) {
			$aOutput[] = '<script type="text/javascript" src="'.$sFile.'"></script>';
		}

		foreach( self::$aClientScriptBlocks as $sKey => $aClientScriptBlock ) {
			$aOutput[] = '<script type="text/javascript">';
			$aOutput[] = '//'.$aClientScriptBlock[0].' ('.$sKey.')';
			$aOutput[] = $aClientScriptBlock[1];
			$aOutput[] = '</script>';
		}
		wfProfileOut( 'BS::'.__METHOD__ );
		return implode("\n", $aOutput);
	}
	
	public static function getFileRegister() {
		return self::$oFileManager->getFileRegister();
	}
	
	public static function getClientScriptBlocks() {
		return self::$aClientScriptBlocks;
	}

	/**
	 * Use this to place javascript logic _below_ the including script files. Therefore you can benefit from the available frameworks like BlueSpiceFramework, ExtJS and jQuery.
	 * @param String $sExtensionKey The name of the extension. This is just for creating a nice comment within the script-Tags
	 * @param String $sCode The JavaScript code, that should be executed after all scriptfiles have been included
	 * @param String $sUniqueKey (Optional) If provided the script block gets saved in with a unique key and therefore will not be registered multiple times.
	 */
	public static function registerClientScriptBlock( $sExtensionKey, $sCode, $sUniqueKey = '' ) {
		if( !empty( $sUniqueKey ) ) {
			self::$aClientScriptBlocks[$sUniqueKey] = array( $sExtensionKey, $sCode );
		} else {
			self::$aClientScriptBlocks[] = array( $sExtensionKey, $sCode );
		}
	}
}