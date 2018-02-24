<?php

namespace BlueSpice\Permission;

/**
 *
 * This class contains description
 * of a single permission
 */
class Description implements IDescription {
	protected $name;
	protected $type = 'namespace';
	protected $preventLockout = false;
	protected $dependencies = [];
	protected $roles = [];
	protected $configArray = [];

	/**
	 *
	 * @param string $name
	 * @param array $config
	 * @return void
	 */
	public function __construct( $name, $config = [] ) {
		$this->name = $name;

		if( isset( $config ) === false ) {
			return;
		}
		if( isset( $config[ 'type' ] ) ) {
			$this->type = $config[ 'type' ];
		}
		if( isset( $config[ 'preventLockout' ] ) ) {
			$this->preventLockout = $config[ 'preventLockout' ];
		}
		if( isset( $config[ 'dependencies' ] ) &&
			is_array( $config[ 'dependencies' ] ) ) {
			$this->dependencies = $config[ 'dependencies' ];
		}
		if( isset( $config[ 'roles' ] ) &&
			is_array( $config[ 'roles' ] ) ) {
			$this->roles = $config[ 'roles' ];
		}

		$this->configArray = $config;
	}

	/**
	 * Gets permission name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Gets permission type
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Return true if this permission
	 * cannot be removed without locking out
	 * the user
	 * @return bool
	 */
	public function getPreventLockout() {
		return $this->preventLockout;
	}

	/**
	 * Gets permissions that depend
	 * on this permission
	 *
	 * @return array
	 */
	public function getDependencies() {
		return $this->dependencies;
	}

	/**
	 * Returns roles this permission
	 * belongs to
	 *
	 * @return array
	 */
	public function getRoles() {
		return $this->roles;
	}

	/**
	 * Generic method to return custom properties
	 * of permission config array
	 *
	 * @param string $propertyName
	 * @return string|false if property doesn't exist
	 */
	public function getProperty( $propertyName ) {
		if( isset( $this->configArray[ $propertyName ] ) ) {
			return $this->configArray[ $propertyName ];
		} else {
			return false;
		}
	}
}
