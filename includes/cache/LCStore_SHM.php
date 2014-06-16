<?php

/**
 * This file is part of blue spice for MediaWiki.
 *
 * @copyright Copyright (c) 2010, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Sebastian Ulbricht <sebastian.ulbricht@dragon-network.hk>
 * @version 0.1.0 beta
 *
 * $LastChangedDate: 2011-09-06 17:46:46 +0800 (Tue, 06 Sep 2011) $
 * $LastChangedBy: rvogel $
 * $Rev: 3072 $

 */
class LCStore_SHM implements LCStore {

	protected static
			$_sCurrentLanguage,
			$_aLoadedLanguages,
			$_aData = array();
	protected
	$readOnly = true,
	$_sDirectory;

	public function __construct( $aConf ) {
		global $wgCacheDirectory;
		if ( isset( $aConf[ 'directory' ] ) && $aConf[ 'directory' ] ) {
			$this->_sDirectory = $aConf[ 'directory' ];
		} else {
			$this->_sDirectory = $wgCacheDirectory;
		}
	}

	public function get( $code, $key ) {
		if ( !isset( self::$_aLoadedLanguages[ $code ] ) || !self::$_aLoadedLanguages[ $code ] ) {
			$this->loadLanguage( $code );
		}
		if ( !isset( self::$_aData[ $code ] ) || !isset( self::$_aData[ $code ][ $key ] ) ) {
			return null;
		}
		return self::$_aData[ $code ][ $key ];
	}

	public function startWrite( $code ) {
		$this->readOnly = false;
		self::$_sCurrentLanguage = $code;
		self::$_aData = array();
		self::$_aData[ self::$_sCurrentLanguage ] = array( );
	}

	public function finishWrite() {
		if ( $this->readOnly ) {
			return;
		}
		$sFilename = $this->_sDirectory . DS . self::$_sCurrentLanguage . '.bscache';
		$sSerializedData = serialize( $this->_aData[ self::$_sCurrentLanguage ] );
		file_put_contents( $sFilename, $sSerializedData, LOCK_EX );
		if ( is_callable( 'ftok' ) ) {
			$iShmKey = ftok( $sFilename, 'b' );
			$iShmSize = strlen( $sSerializedData ) * 2;
			$rShmResource = shm_attach( $iShmKey, $iShmSize );
			if ( $rShmResource ) {
				shm_remove( $rShmResource );
				shm_detach( $rShmResource );
				$rShmResource = shm_attach( $iShmKey, $iShmSize );
				shm_put_var( $rShmResource, 1, $sSerializedData );
				shm_detach( $rShmResource );
			}
		}
		unset( self::$_aData[ self::$_sCurrentLanguage ] );
	}

	public function loadLanguage( $code ) {
		$sFilename = $this->_sDirectory . DS . $code . '.bscache';
		if ( !file_exists( $sFilename ) ) {
			return;
		}
		if ( is_callable( 'ftok' ) ) {
			$iShmKey = ftok( $sFilename, 'b' );
			$rShmResource = shm_attach( $iShmKey );
			if ( $rShmResource ) {
				if ( !shm_has_var( $rShmResource, 1 ) ) {
					shm_remove( $rShmResource );
				} else {
					$sData = shm_get_var( $rShmResource, 1 );
					if ( $sData ) {
						self::$_aData[ $code ] = unserialize( $sData );
					}
				}
			}
		}
		if ( !isset( self::$_aData[ $code ] ) || !is_array( self::$_aData[ $code ] ) || !count( self::$_aData[ $code ] ) ) {
			$sData = file_get_contents( $sFilename );
			if ( $sData ) {
				self::$_aData[ $code ] = unserialize( $sData );
			}
		}
		self::$_aLoadedLanguages[ $code ] = true;
	}

	public function set( $key, $value ) {
		if ( $this->readOnly ) {
			return;
		}

		self::$_aData[ self::$_sCurrentLanguage ][ $key ] = $value;
	}

}