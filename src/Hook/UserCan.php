<?php

namespace BlueSpice\Hook;

abstract class UserCan extends Hook {

	/**
	 *
	 * @var \Title
	 */
	protected $title  = null;

	/**
	 *
	 * @var \User
	 */
	protected $user = null;

	/**
	 *
	 * @var string
	 */
	protected $action = '';

	/**
	 *
	 * @var boolean
	 */
	protected $result = false;

	/**
	 *
	 * @param \Title $title
	 * @param \User $user
	 * @param string $action
	 * @param boolean $result
	 * @return boolean
	 */
	public static function callback( &$title, &$user, $action, &$result ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$title,
			$user,
			$action,
			$result
		);
		return $hookHandler->process();
	}

	public function __construct( $context, $config, &$title, &$user, $action, &$result ) {
		parent::__construct( $context, $config );

		$this->title = $title;
		$this->user = $user;
		$this->action = $action;
		$this->result =& $result;
	}
}