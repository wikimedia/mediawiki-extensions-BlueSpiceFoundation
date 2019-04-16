<?php

namespace BlueSpice;

use Title;
use User;
use BlueSpice\ExtensionAttributeBasedRegistry;
use Config;
use IContextSource;
use BlueSpice\Services;
use BlueSpice\Permission\Lockdown;

class PermissionLockdownFactory {

	/**
	 *
	 * @var ExtensionAttributeBasedRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @var IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @var Lockdown[]
	 */
	protected $instances = [];

	/**
	 *
	 * @param ExtensionAttributeBasedRegistry $registry
	 * @param Config $config
	 * @param IContextSource $context
	 */
	public function __construct( ExtensionAttributeBasedRegistry $registry, Config $config, IContextSource $context ) {
		$this->registry = $registry;
		$this->config = $config;
		$this->context = $context;
	}

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @return Lockdown
	 */
	public function newFromTitleAndUserRelation( Title $title, User $user ) {
		if( $instance = $this->getLockownFromCache( $title, $user ) ) {
			return $instance;
		}
		return $this->newLockdown( $title, $user );
	}

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @return Lockdown
	 */
	protected function newLockdown( Title $title, User $user ) {
		return new Lockdown( $this->config, $title, $user, $this->getModules( $user ) );
	}

	/**
	 *
	 * @param User $user
	 * @param Module[] $modules
	 * @return Module[]
	 */
	protected function getModules( User $user, array $modules = [] ) {
		foreach( $this->registry->getAllKeys() as $key ) {
			$modules[] = call_user_func_array( $this->registry->getValue( $key ), [
				$this->config,
				$this->context,
				Services::getInstance(),
			] );
		}
		return $modules;
	}

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @return Lockdown|false
	 */
	protected function getLockownFromCache( Title $title, User $user ) {
		if( isset( $this->instances[$this->getCacheKey( $title, $user )] ) ) {
			return $this->instances[$this->getCacheKey( $title, $user )];
		}
		return false;
	}

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @return string
	 */
	protected function getCacheKey( Title $title, User $user ) {
		return "{$title->getArticleID()}-{$user->getId()}";
	}

}
