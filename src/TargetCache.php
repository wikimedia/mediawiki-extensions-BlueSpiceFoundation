<?php

namespace BlueSpice;

use BlueSpice\TargetCacheHandler\ITarget;
use BlueSpice\Utility\CacheHelper;
use MediaWiki\Config\Config;

abstract class TargetCache implements ITargetCache {

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @var CacheHelper
	 */
	protected $cacheHelper = null;

	/**
	 *
	 * @var ITargetCacheHandler[]
	 */
	protected $instances = [];

	/**
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 *
	 * @var \BlueSpice\ExtensionAttributeBasedRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @param string $type
	 * @param Config $config
	 * @param CacheHelper $cacheHelper
	 */
	public function __construct( $type, Config $config, CacheHelper $cacheHelper ) {
		$this->type = $type;
		$this->config = $config;
		$this->cacheHelper = $cacheHelper;
	}

	/**
	 *
	 * @return IRegistry
	 */
	protected function getRegistry() {
		if ( $this->registry ) {
			return $this->registry;
		}
		$this->registry = new \BlueSpice\ExtensionAttributeBasedRegistry(
			$this->getRegistryKey()
		);
		return $this->registry;
	}

	/**
	 *
	 * @param string $key
	 * @param ITarget $target
	 * @return ITargetCacheHandler
	 */
	public function getHandler( $key, $target ) {
		if ( isset( $this->instances[$key][$target->getIdentifier()] ) ) {
			return $this->instances[$key][$target->getIdentifier()];
		}
		$className = $this->getRegistry()->getValue(
			$key,
			false
		);
		$this->instances[$key][$target->getIdentifier()] = new $className(
			$this->type,
			$key,
			$this->cacheHelper,
			$target
		);

		return $this->instances[$key][$target->getIdentifier()];
	}

	/**
	 *
	 * @param ITarget $target
	 * @param string $action
	 */
	public function invalidateAll( $target, $action = '' ) {
		foreach ( $this->getRegistry()->getAllKeys() as $key ) {
			$this->getHandler( $key, $target )->invalidate( $action );
		}
	}
}
