<?php

namespace BlueSpice;

use MediaWiki\Title\Title;
use MediaWiki\User\User;

class NullLogger extends ActionLogger {

	/**
	 *
	 * @param string|null $type
	 * @param User|null $performer
	 * @param Title|null $target
	 */
	public function __construct( $type = null, $performer = null, $target = null ) {
	}

	/**
	 *
	 * @param string $action
	 * @param array $params
	 * @param array $options
	 * @param bool $publish Whether to list in recent changes or not
	 * @return int
	 */
	public function log( $action, $params, $options = [], $publish = false ) {
		return 0;
	}
}
