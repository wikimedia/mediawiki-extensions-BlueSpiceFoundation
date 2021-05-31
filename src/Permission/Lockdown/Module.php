<?php

namespace BlueSpice\Permission\Lockdown;

use BlueSpice\IServiceProvider;
use Config;
use IContextSource;
use MediaWiki\MediaWikiServices;
use MediaWiki\User\UserGroupManager;
use Message;
use MessageLocalizer;
use User;

abstract class Module implements IModule, IServiceProvider, MessageLocalizer {

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
	 * @var MediaWikiServices
	 */
	protected $services = null;

	/**
	 *
	 * @var string[]
	 */
	protected static $userGroups = null;

	/**
	 *
	 * @param Config $config
	 * @param IContextSource $context
	 * @param MediaWikiServices $services
	 */
	protected function __construct( Config $config, IContextSource $context,
		MediaWikiServices $services ) {
		$this->config = $config;
		$this->context = $context;
		$this->services = $services;
	}

	/**
	 *
	 * @param Config $config
	 * @param IContextSource $context
	 * @param MediaWikiServices $services
	 * @return Module
	 */
	public static function getInstance( Config $config, IContextSource $context,
		MediaWikiServices $services ) {
		return new static( $config, $context, $services );
	}

	/**
	 *
	 * @return MediaWikiServices
	 */
	public function getServices() {
		return $this->services;
	}

	/**
	 * @param string|string[]|MessageSpecifier $key Message key, or array of keys,
	 *   or a MessageSpecifier.
	 * @param mixed ...$params Normal message parameters
	 * @return Message
	 */
	public function msg( $key, ...$params ) {
		return $this->getContext()->msg( $key, ...$params );
	}

	/**
	 *
	 * @return Context
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 *
	 * @return Config
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 *
	 * @param User $user
	 * @return string[]
	 */
	protected function getUserGroups( User $user ) {
		if ( isset( static::$userGroups[$user->getId()] ) ) {
			return static::$userGroups[$user->getId()];
		}
		static::$userGroups[$user->getId()] = MediaWikiServices::getInstance()
			->getUserGroupManager()
			->getUserEffectiveGroups( $user, UserGroupManager::READ_NORMAL, true );
		return static::$userGroups[$user->getId()];
	}

}
