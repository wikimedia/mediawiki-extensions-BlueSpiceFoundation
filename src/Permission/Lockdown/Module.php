<?php

namespace BlueSpice\Permission\Lockdown;

use User;
use Config;
use Message;
use BlueSpice\Services;
use IContextSource;
use BlueSpice\IServiceProvider;
use MessageLocalizer;

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
	 * @var Services
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
	 * @param Services $services
	 */
	protected function __construct( Config $config, IContextSource $context, Services $services ) {
		$this->config = $config;
		$this->context = $context;
		$this->services = $services;
	}

	/**
	 *
	 * @param Config $config
	 * @param IContextSource $context
	 * @param Services $services
	 * @return Module
	 */
	public static function getInstance( Config $config, IContextSource $context, Services $services ) {
		return new static( $config, $context, $services );
	}

	/**
	 *
	 * @return Services
	 */
	public function getServices() {
		return $this->services;
	}

	/**
	 * @param string|string[]|MessageSpecifier $key Message key, or array of keys,
	 *   or a MessageSpecifier.
	 * @param mixed $params,... Normal message parameters
	 * @return Message
	 */
	public function msg( $key ) {
		return call_user_func_array(
			[ $this->getContext(), 'msg' ],
			func_get_args()
		);
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
		static::$userGroups[$user->getId()] = $user->getEffectiveGroups( true );
		return static::$userGroups[$user->getId()];
	}

}
