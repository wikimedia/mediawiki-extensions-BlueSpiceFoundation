<?php
/**
 * This file is part of blue spice for MediaWiki.
 *
 * @abstract
 * @copyright Copyright (c) 2007-2009, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Sebastian Ulbricht, Robert Vogel, Stephan Muggli
 * @version 1.20.2
 *
 * $LastChangedDate $
 * $LastChangedBy $
 * $Rev $
 * $Id $
 */

// Last review MRG20100813

/**
 * the BsStyleManager
 * @package BlueSpice_Core
 * @subpackage Core
 */
class BsStyleManager {

	protected static $prInlineStyles = array();
	protected static $oFileManager = NULL;

	public static function init() {
		self::$oFileManager = new BsFileManager();
	}

	// TODO MRG20100813: Was bedeutet 0? Kann man das durch eine Konstante ersezten?
	public static function add($group, $file, $options = 0) {
		self::$oFileManager->add($group, $file, $options);
	}

	public static function addStyleBlock($styletext, $media = BsSTYLEMEDIA::SCREEN) {
		self::$prInlineStyles[$media][] = $styletext;
	}

	public static function getOutput() {
		wfProfileIn( 'BS::'.__METHOD__ );

		$aOutput = array();
		foreach( self::$prInlineStyles as $media => $styletexts ) {
			$aOutput[] = '<style type="text/css" media="'.self::getMediaTypes( $media ).'">';
			$aOutput[] = implode( "\n", $styletexts );
			$aOutput[] = '</style>';
		}

		$sOut = implode( "\n", $aOutput );
		$sOut .= self::output();

		wfProfileOut( 'BS::'.__METHOD__ );
		return $sOut;
	}

	protected static function output() {
		wfProfileIn( 'BS::'.__METHOD__ );
		$aOutput = array();
		$aFileRegister = self::$oFileManager->getFileRegister();

		foreach ( $aFileRegister as $sFile => $iOptions ) {
			$aOutput[] = '<link rel="stylesheet" type="text/css" media="'.self::getMediaTypes( $iOptions ).'" href="'.$sFile.'" />';
		}

		wfProfileOut( 'BS::'.__METHOD__ );
		return implode( "\n", $aOutput );
	}
	
	public static function getFileRegister() {
		return self::$oFileManager->getFileRegister();
	}
	
	public static function getInlineStyles() {
		return self::$prInlineStyles;
	}

	public static function getMediaTypes($media) {
		if ( $media == BsSTYLEMEDIA::ALL || !$media ) {
			return 'all';
		}
		$aOutput = array();
		if ( $media & BsSTYLEMEDIA::AURAL ) {
			$aOutput[] = 'aural';
		}
		if ( $media & BsSTYLEMEDIA::BRAILLE ) {
			$aOutput[] = 'braille';
		}
		if ( $media & BsSTYLEMEDIA::HANDHELD ) {
			$aOutput[] = 'handheld';
		}
		if ( $media & BsSTYLEMEDIA::PRINTER ) {
			$aOutput[] = 'print';
		}
		if ( $media & BsSTYLEMEDIA::PROJECTION ) {
			$aOutput[] = 'projection';
		}
		if ( $media & BsSTYLEMEDIA::SCREEN ) {
			$aOutput[] = 'screen';
		}
		if ( $media & BsSTYLEMEDIA::TTY ) {
			$aOutput[] = 'tty';
		}
		if ( $media & BsSTYLEMEDIA::TV ) {
			$aOutput[] = 'tv';
		}
		return implode(' ', $aOutput);
	}

}