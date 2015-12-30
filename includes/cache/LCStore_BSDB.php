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
class LCStore_BSDB implements LCStore {

	var $currentLang;
	var $cache = array();
	var $writesDone = false;
	var $dbw, $batch;
	var $readOnly = false;

	public function get ( $code, $key ) {
		wfProfileIn( 'LocalisationCache: ' . __METHOD__ );
		if ( !isset( $this->cache[ $code ] ) ) {
			$this->cache[$code] = array();
			if ( $this->writesDone ) {
				$db = wfGetDB( DB_MASTER );
			} else {
				$db = wfGetDB( DB_SLAVE );
			}

			$res = $db->select( 'l10n_cache', array( 'lc_key', 'lc_value' ), array( 'lc_lang' => $code ), __METHOD__);
			while($row = $res->fetchRow()) {
				$this->cache[$code][$row['lc_key']] = $row['lc_value'];
			}
		}
		if(!isset($this->cache[$code]['$key'])) {
			wfProfileOut( 'LocalisationCache: ' . __METHOD__ );
			return null;
		}
		wfProfileOut( 'LocalisationCache: ' . __METHOD__ );
		return unserialize($this->cache[$code][$key]);

		$row = $db->selectRow( 'l10n_cache', array( 'lc_value' ), array( 'lc_lang' => $code, 'lc_key' => $key ), __METHOD__ );
		if ( $row ) {
			wfProfileOut( 'LocalisationCache: ' . __METHOD__ );
			return unserialize( $row->lc_value );
		} else {
			wfProfileOut( 'LocalisationCache: ' . __METHOD__ );
			return null;
		}
	}

	public function startWrite ( $code ) {
		if ( $this->readOnly ) {
			return;
		}
		if ( !$code ) {
			throw new MWException( __METHOD__ . ": Invalid language \"$code\"" );
		}
		$this->dbw = wfGetDB( DB_MASTER );
		try {
			$this->dbw->begin( __METHOD__ );
			$this->dbw->delete( 'l10n_cache', array( 'lc_lang' => $code ), __METHOD__ );
		} catch ( DBQueryError $e ) {
			if ( $this->dbw->wasReadOnlyError() ) {
				$this->readOnly = true;
				$this->dbw->rollback( __METHOD__ );
				if ( is_callable( array( $this->dbw, 'ignoreErrors' ) ) ) {
					$this->dbw->ignoreErrors( false );
				}
				return;
			} else {
				throw $e;
			}
		}
		$this->currentLang = $code;
		$this->batch = array( );
	}

	public function finishWrite () {
		if ( $this->readOnly ) {
			return;
		}
		if ( $this->batch ) {
			$this->dbw->insert( 'l10n_cache', $this->batch, __METHOD__ );
		}
		$this->dbw->commit( __METHOD__ );
		$this->currentLang = null;
		$this->dbw = null;
		$this->batch = array( );
		$this->writesDone = true;
	}

	public function set ( $key, $value ) {
		if ( $this->readOnly ) {
			return;
		}
		if ( is_null( $this->currentLang ) ) {
			throw new MWException( __CLASS__ . ': must call startWrite() before calling set()' );
		}
		$this->batch[ ] = array(
			'lc_lang' => $this->currentLang,
			'lc_key' => $key,
			'lc_value' => serialize( $value ) );
		if ( count( $this->batch ) >= 100 ) {
			$this->dbw->insert( 'l10n_cache', $this->batch, __METHOD__ );
			$this->batch = array( );
		}
	}

}