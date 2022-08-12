<?php

namespace BlueSpice;

use Status;

interface IDynamicSettings {

	/**
	 * Does whatever is required to setup the application
	 *
	 * @param array &$globals usually $GLOBALS
	 * @return void
	 * @deprecated 4.3 - Passing $GLOBALS as reference not supported by PHP 8.1 anymore.
	 * All implementations of `IDynamicSettings` should access `$GLOBALS` directly.
	 */
	public function apply( &$globals );

	/**
	 * Set whatever data to be persited
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function setData( $data );

	/**
	 *
	 * @return Status
	 */
	public function persist();

	/**
	 *
	 * @return mixed
	 */
	public function fetch();
}
