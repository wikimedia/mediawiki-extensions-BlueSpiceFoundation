<?php

namespace BlueSpice;

interface IRegistry {

	/**
	 *
	 * @param string $key
	 * @param string $default
	 * @return string
	 */
	public function getValue( $key, $default = '' );
}