<?php

namespace BlueSpice\StatusCheck;

use BlueSpice\InstanceStatus\IStatusProvider;
use ObjectCacheFactory;
use Wikimedia\ObjectCache\MemcachedBagOStuff;

class MemcachedStatusCheckProvider implements IStatusProvider {

	/** @var ObjectCacheFactory */
	private $objectCacheFactory;

	/**
	 * @param ObjectCacheFactory $objectCacheFactory
	 */
	public function __construct( ObjectCacheFactory $objectCacheFactory ) {
		$this->objectCacheFactory = $objectCacheFactory;
	}

	/**
	 * @return string
	 */
	public function getLabel(): string {
		return 'ext-bluespicefoundation-memcached-connectivity';
	}

	/**
	 * @return string
	 */
	public function getValue(): string {
		$cacheType = $GLOBALS['wgMainCacheType'];
		if ( !in_array( $cacheType, [ CACHE_MEMCACHED, 'memcached-pecl' ] ) ) {
			return "wgMainCacheType set to $cacheType";
		}

		if ( empty( $GLOBALS['wgMemCachedServers'] ) ) {
			return "wgMemCachedServers empty";
		}

		$cache = $this->objectCacheFactory->getInstance( $cacheType );
		if ( !$cache instanceof MemcachedBagOStuff ) {
			return "Cache instance is not MemcachedBagOStuff, got " . get_class( $cache );
		}

		$value = $cache->getWithSetCallback(
			$cache->makeKey( 'memcached', 'healthcheck' ),
			$cache::TTL_PROC_SHORT,
			static function () {
				return 'OK';
			}
		);

		return $value === 'OK' ? 'OK' : "Cache returned value: $value";
	}

	/**
	 * @return string
	 */
	public function getIcon(): string {
		return 'check';
	}

	/**
	 * @return int
	 */
	public function getPriority(): int {
		return 100;
	}
}
