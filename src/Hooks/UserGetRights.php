<?php

namespace BlueSpice\Hooks;

abstract class UserGetRights extends Hook {

	/**
	 *
	 * @var \User
	 */
	protected $user = null;

	/**
	 *
	 * @var array
	 */
	protected $rights = [];

	/**
	 *
	 * @param \User $user
	 * @param array $rights
	 * @return type
	 */
	public static function callback( $user, &$rights ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$user,
			$rights
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \User $user
	 * @param array $rights
	 */
	public function __construct( $context, $config, $user, &$rights ) {
		parent::__construct( $context, $config );

		$this->user = $user;
		$this->rights =& $rights;
	}
}