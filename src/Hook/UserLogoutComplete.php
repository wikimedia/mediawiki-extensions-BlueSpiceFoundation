<?php

namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\User\User;

abstract class UserLogoutComplete extends Hook {

	/**
	 * The user object after logout (won't have name, ID, etc.)
	 * @var User|null
	 */
	protected $user = null;

	/**
	 * Any HTML to inject after the logout success message
	 * @var string|null
	 */
	protected $inject_html = null;

	/**
	 * The text of the username that just logged out
	 * @var string|null
	 */
	protected $old_name = null;

	/**
	 * @param User &$user
	 * @param string &$inject_html
	 * @param string $old_name
	 * @return bool
	 */
	public static function callback( &$user, &$inject_html, $old_name ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$user,
			$inject_html,
			$old_name
		);
		return $hookHandler->process();
	}

	/**
	 * @param IContextSource $context
	 * @param Config $config
	 * @param User &$user
	 * @param string &$inject_html
	 * @param string $old_name
	 */
	public function __construct( $context, $config, &$user, &$inject_html, $old_name ) {
		parent::__construct( $context, $config );

		$this->user = &$user;
		$this->inject_html = &$inject_html;
		$this->old_name = $old_name;
	}
}
