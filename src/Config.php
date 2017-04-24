<?php

namespace BlueSpice;

class Config extends \GlobalVarConfig {
	public static function newInstance() {
		return new self( 'bsg' );
	}
}
