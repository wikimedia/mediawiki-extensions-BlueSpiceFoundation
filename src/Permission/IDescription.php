<?php

namespace BlueSpice\Permission;

interface IDescription {
	/**
	 * @param string $name
	 * @param array $config
	 */
	public function __construct( $name, $config );

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @return bool
	 */
	public function getPreventLockout();

	/**
	 * @return array
	 */
	public function getDependencies();

	/**
	 * @return array
	 */
	public function getRoles();

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getProperty( $name );
}
