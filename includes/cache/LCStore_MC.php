<?php

/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Stefan Widmann <widmann@hallowelt.com>
  */
/**
 * LCStore implementation which uses memcache to store data.
 */
class LCStore_MC implements LCStore {
	private $currentLang;
	private $keys;

	public function __construct() {
		$this->cache = wfGetCache( CACHE_MEMCACHED );
	}

	public function get( $code, $key ) {
		$k = wfMemcKey( 'l10n', $code, 'k', $key );
		$r = $this->cache->get( $k );

		return $r === false ? null : $r;
	}

	public function startWrite( $code ) {
		$k = wfMemcKey( 'l10n', $code, 'l' );
		$keys = $this->cache->get( $k );
		if ( $keys ) {
			foreach ( $keys as $k ) {
				$this->cache->delete( $k );
			}
		}
		$this->currentLang = $code;
		$this->keys = array();
	}

	public function finishWrite() {
		if ( $this->currentLang ) {
			$k = wfMemcKey( 'l10n', $this->currentLang, 'l' );
			$this->cache->set( $k, array_keys( $this->keys ) );
		}
		$this->currentLang = null;
		$this->keys = array();
	}

	public function set( $key, $value ) {
		if ( $this->currentLang ) {
			$k = wfMemcKey( 'l10n', $this->currentLang, 'k', $key );
			$this->keys[$k] = true;
			$this->cache->set( $k, $value );
		}
	}
}