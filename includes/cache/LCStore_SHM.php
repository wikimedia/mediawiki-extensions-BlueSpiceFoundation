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
 * $Id: LoggerAppenderMW.php 3072 2011-09-06 09:46:46Z rvogel $
 */
class LCStore_SHM implements LCStore {

	protected
	$readOnly = true,
	$_sDirectory,
	$_sCurrentLanguage,
	$_aLoadedLanguages,
	$_aData = array( );

	public function __construct( $aConf ) {
		global $wgCacheDirectory;
		if ( isset( $aConf[ 'directory' ] ) && $aConf[ 'directory' ] ) {
			$this->_sDirectory = $aConf[ 'directory' ];
		} else {
			$this->_sDirectory = $wgCacheDirectory;
		}
	}

	public function get( $code, $key ) {
		if ( !isset( $this->_aLoadedLanguages[ $code ] ) || !$this->_aLoadedLanguages[ $code ] ) {
			$this->loadLanguage( $code );
		}
		if ( !isset( $this->_aData[ $code ][ $key ] ) ) {
			return null;
		}
		return $this->_aData[ $code ][ $key ];
	}

	public function startWrite( $code ) {
		$this->readOnly = false;
		$this->_sCurrentLanguage = $code;
		$this->_aData = array();
		$this->_aData[ $this->_sCurrentLanguage ] = array( );
	}

	public function finishWrite() {
		if ( $this->readOnly ) {
			return;
		}
		$sFilename = $this->_sDirectory . DS . $this->_sCurrentLanguage . '.bscache';
		$sSerializedData = serialize( $this->_aData[ $this->_sCurrentLanguage ] );
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
		unset( $this->_aData[ $this->_sCurrentLanguage ] );
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
						$this->_aData[ $code ] = unserialize( $sData );
					}
				}
			}
		}
		if ( !isset( $this->_aData[ $code ] ) || !is_array( $this->_aData[ $code ] ) || !count( $this->_aData[ $code ] ) ) {
			$sData = file_get_contents( $sFilename );
			if ( $sData ) {
				$this->_aData[ $code ] = unserialize( $sData );
			}
		}
		$this->_aLoadedLanguages[ $code ] = true;
	}

	public function set( $key, $value ) {
		if ( $this->readOnly ) {
			return;
		}

		$this->_aData[ $this->_sCurrentLanguage ][ $key ] = $value;
	}

}