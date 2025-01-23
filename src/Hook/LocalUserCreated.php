<?php

namespace BlueSpice\Hook;

use MediaWiki\Config\Config;
use MediaWiki\User\User;

abstract class LocalUserCreated extends \BlueSpice\Hook {
	/**
	 *
	 * @var User
	 */
	protected $user = null;

	/**
	 *
	 * @var bool
	 */
	protected $autocreated = null;

	/**
	 * Adds additional data to links generated by the framework. This allows us
	 * to add more functionality to the UI.
	 * @param User $user
	 * @param bool $autocreated
	 * @return bool Always true to keep hook running
	 */
	public static function callback( User $user, $autocreated ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$user,
			$autocreated
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param Config $config
	 * @param User $user
	 * @param bool $autocreated
	 */
	public function __construct( $context, $config, User $user, $autocreated ) {
		parent::__construct( $context, $config );

		$this->user = $user;
		$this->autocreated = $autocreated;
	}
}
