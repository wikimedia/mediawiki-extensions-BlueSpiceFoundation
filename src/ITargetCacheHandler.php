<?php

namespace BlueSpice;

interface ITargetCacheHandler {

	/**
	 * @return mixed|false
	 */
	public function get();

	/**
	 *
	 * @param mixed $data
	 */
	public function set( $data );

	/**
	 *
	 * @param string $action
	 * @return bool
	 */
	public function invalidate( $action = '' );

}
