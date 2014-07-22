<?php
/**
 * This class contains methods working with mediawiki's cache .
 *
 * @copyright Copyright (c) 2007-2014, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Stefan Widmann <widmann@hallowelt.biz>
 * @version 0.1.0 beta
 */
class BsCacheHelper {

	protected static $aCacheKeys = array();
	protected static $oCache = null;

	/**
	 * gets cache which is set in $wgMainCacheType
	 * @return BagOStuff
	 */
	public static function getCache() {
		if( self::$oCache === null ) {
			self::$oCache = wfGetCache( CACHE_ANYTHING );
		}

		return self::$oCache;
	}

	/**
	 * Get a cache key, depending on given params like getCacheKey( 'MyExtension', 'MyStuff', myID, etc )
	 * Use of multiple params (func_get_args)
	 * @return string
	 */
	public static function getCacheKey( /*...*/ ) {
		$args = func_get_args();
		$sKey = call_user_func_array( 'wfMemcKey', $args );
		self::$aCacheKeys[] = $sKey;

		return $sKey;
	}

	/**
	 * Get the value of a given key or returns false
	 * @param type $sKey
	 * @return type
	 */
	public static function get( $sKey ) {
		return self::getCache()->get( $sKey );
	}

	/**
	 * Sets the value to the given key
	 * @param type $sKey
	 * @param type $mData
	 * @param type $iExpiryTime
	 * @return type
	 */
	public static function set( $sKey, $mData, $iExpiryTime = 0 ) {
		return self::getCache()->set( $sKey, $mData, $iExpiryTime );
	}

	/**
	 * Invalidate cache for given key
	 * @param mixed $mKey
	 * @return boolean | true if key is deleted false if at least one key failed to invalidate
	 */
	public static function invalidateCache( $mKey ) {
		if( is_array( $mKey ) ) {
			$bReturn = true;
			foreach( $mKey as $key ) {
				if( !self::getCache()->delete( $key ) ) {
					wfDebugLog( 'BsMemcached', 'NO INVALIDATION FOR KEY: '.$key );
					$bReturn = false;
				}
			}
			return $bReturn;
		}
		return self::getCache()->delete( $mKey );
	}

	/**
	 * invalidates caches for all keys registered with BsCacheHelper::getCacheKey()
	 */
	public static function invalidateAllCaches() {
		self::invalidateCache( self::$aCacheKeys );
	}

	/**
	 * cache invalidation
	 * @param Article $article
	 * @param User $user
	 * @param Content $content
	 * @param type $summary
	 * @param bool $isMinor
	 * @param bool $isWatch
	 * @param type $section
	 * @param type $flags
	 * @param Revision $revision
	 * @param Status $status
	 * @param type $baseRevId
	 * @return boolean
	 */
	public static function onPageContentSaveComplete( $article, $user, $content, $summary, $isMinor, $isWatch, $section, $flags, $revision, $status, $baseRevId ) {
		// This is hardcoded because there is no need to make it generic, yet
		//invalidate cache ArticleHelper::getDiscussionAmount
		if( $article->getTitle()->isTalkPage() ) {
			BsCacheHelper::invalidateCache( BsCacheHelper::getCacheKey( 'BlueSpice', 'ArticleHelper', 'getDiscussionAmount', $article->getTitle()->getArticleID() ) );
		} else {
			$aKeys = array(
				// invalidate cache ArticleHelper::loadPageProps
				BsCacheHelper::getCacheKey( 'BlueSpice', 'ArticleHelper', 'loadPageProps', $article->getTitle()->getArticleID() ),
				// invalidate cache WidgetListHelper::getWidgets
				BsCacheHelper::getCacheKey( 'BlueSpice', 'WidgetListHelper', $article->getTitle()->getPrefixedDBkey() )
			);

			BsCacheHelper::invalidateCache( $aKeys );
		}
		return true;
	}

	/**
	 *
	 * @param BsConfig $oBsConfig
	 * @param type $aSettings
	 */
	public static function onBsSettingsAfterSaveSettings( $oBsConfig, $aSettings ) {
		$aKeys = array(
			BsCacheHelper::getCacheKey( 'BlueSpice', 'BsConfig', 'loadSettings' )
		);

		BsCacheHelper::invalidateCache( $aKeys );

		return true;
	}

}