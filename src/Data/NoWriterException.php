<?php

namespace BlueSpice\Data;

use \Exception;

class NoWriterException extends Exception {
	public function __construct( $code = 0, \Throwable $previous = null ) {
		parent::__construct( 'This store does not support writing!', $code, $previous );
	}
}
