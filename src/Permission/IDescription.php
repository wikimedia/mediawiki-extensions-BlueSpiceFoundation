<?php

namespace BlueSpice\Permission;

interface IDescription {
	/**
	 * @param string
	 * @param array
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
	 * @param string
	 * @return mixed
	 */
	public function getProperty( $name );
}
