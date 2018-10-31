<?php

use BlueSpice\Services;

/**
 * This class contains methods working with mediawiki's cache .
 *
 * @deprecated since version 3.1.0 - Use Services::getInstance()
 * ->getBSUtilityFactory()->getCacheHelper() instead
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Stefan Widmann <widmann@hallowelt.com>
 */
class BsCacheHelper {

	/**
	 * gets cache which is set in $wgMainCacheType
	 * @deprecated since version 3.1.0 - Use Services::getInstance()
	 * ->getBSUtilityFactory()->getCacheHelper() instead
	 * @return BagOStuff
	 */
	public static function getCache() {
		return Services::getInstance()->getBSUtilityFactory()->getCacheHelper()
			->getCache();
	}

	/**
	 * Get a cache key, depending on given params like getCacheKey( 'MyExtension', 'MyStuff', myID, etc )
	 * Use of multiple params (func_get_args)
	 * @deprecated since version 3.1.0 - Use Services::getInstance()
	 * ->getBSUtilityFactory()->getCacheHelper() instead
	 * @return string
	 */
	public static function getCacheKey( /*...*/ ) {
		$helper = Services::getInstance()->getBSUtilityFactory()->getCacheHelper();
		$args = func_get_args();
		return call_user_func_array( [$helper, 'getCacheKey'], $args );
	}

	/**
	 * Get the value of a given key or returns false
	 * @deprecated since version 3.1.0 - Use Services::getInstance()
	 * ->getBSUtilityFactory()->getCacheHelper() instead
	 * @param string $sKey
	 * @return mixed
	 */
	public static function get( $sKey ) {
		return Services::getInstance()->getBSUtilityFactory()
			->getCacheHelper()->get( $sKey );
	}

	/**
	 * Sets the value to the given key
	 * @deprecated since version 3.1.0 - Use Services::getInstance()
	 * ->getBSUtilityFactory()->getCacheHelper() instead
	 * @param string $sKey
	 * @param mixed $mData
	 * @param int $iExpiryTime Either an interval in seconds or a unix timestamp for expiry
	 * @return bool
	 */
	public static function set( $sKey, $mData, $iExpiryTime = 0 ) {
		return Services::getInstance()->getBSUtilityFactory()
			->getCacheHelper()->set( $sKey, $mData, $iExpiryTime );
	}

	/**
	 * Invalidate cache for given key
	 * @deprecated since version 3.1.0 - Use Services::getInstance()
	 * ->getBSUtilityFactory()->getCacheHelper() instead
	 * @param mixed $mKey
	 * @return boolean | true if key is deleted false if at least one key failed to invalidate
	 */
	public static function invalidateCache( $mKey ) {
		$helper = Services::getInstance()->getBSUtilityFactory()->getCacheHelper();
		if( is_array( $mKey ) ) {
			$bReturn = true;
			foreach( $mKey as $key ) {
				if( !$helper->invalidate( $key ) ) {
					wfDebugLog( 'BsMemcached', 'NO INVALIDATION FOR KEY: '.$key );
					$bReturn = false;
				}
			}
			return $bReturn;
		}
		return $helper->invalidate( $mKey );
	}

	/**
	 * invalidates caches for all keys registered with BsCacheHelper::getCacheKey()
	 * @deprecated since version 3.1.0 - Was never in use by any extension.
	 */
	public static function invalidateAllCaches() {
		return;
	}

}
