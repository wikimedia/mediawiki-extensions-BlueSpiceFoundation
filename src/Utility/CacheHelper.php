<?php
namespace BlueSpice\Utility;

class CacheHelper {

	/**
	 *
	 * @var \BagOStuff
	 */
	protected $cache = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @param \Config $config
	 */
	public function __construct( \Config $config ) {
		$this->config = $config;
		$this->cache = \ObjectCache::getInstance(
			$this->config->get( 'MainCacheType' )
		);
	}

	/**
	 * gets cache which is set in $wgMainCacheType
	 * @return \BagOStuff
	 */
	public function getCache() {
		return $this->cache;
	}

	/**
	 * Get a cache key, depending on given params like getCacheKey( 'MyExtension', 'MyStuff', myID, etc )
	 * Use of multiple params (func_get_args)
	 * @return string
	 */
	public function getCacheKey( /*...*/ ) {
		return call_user_func_array(
			[ $this->getCache(), 'makeKey' ],
			func_get_args()
		);
	}

	/**
	 * Get the value of a given key or returns false
	 * @param string $key
	 * @return mixed
	 */
	public function get( $key ) {
		return $this->getCache()->get( $key );
	}

	/**
	 * Sets the value to the given key
	 * @param string $key
	 * @param mixed $data
	 * @param int $expiryTime Either an interval in seconds or a unix timestamp for expiry
	 * @return bool
	 */
	public function set( $key, $data, $expiryTime = 0 ) {
		return $this->getCache()->set( $key, $data, $expiryTime );
	}

	/**
	 * Invalidate cache for given key
	 * @param string $key
	 * @return boolean | true if key is deleted false if at least one key failed to invalidate
	 */
	public function invalidate( $key ) {
		$res = $this->getCache()->delete( $key );
		if( !$res ) {
			wfDebugLog( 'BsMemcached', "NO INVALIDATION FOR KEY: $key" );
		}
	}

}
