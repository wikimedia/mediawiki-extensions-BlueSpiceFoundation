<?php

namespace BlueSpice\Data;

interface IRecord {

	/**
	 *
	 * @param string $fieldName
	 * @param mixed|null $default
	 */
	public function get( $fieldName, $default = null );

	/**
	 *
	 * @param string $fieldName
	 * @param mixed $value
	 */
	public function set( $fieldName, $value );

	/**
	 * @return \stdClass
	 */
	public function getData();

	/**
	 * @return \Status
	 */
	public function getStatus();
}
