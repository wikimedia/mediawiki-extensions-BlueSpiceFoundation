<?php

namespace BlueSpice;

class NullLogger extends ActionLogger {

	public function __construct( $type = null, $performer = null, $target = null ) {
	}

	public function log( $action, $params, $options = [], $publish = false ) {
		return 0;
	}
}
