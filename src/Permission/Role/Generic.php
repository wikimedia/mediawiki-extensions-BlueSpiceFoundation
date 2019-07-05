<?php
namespace BlueSpice\Permission\Role;

/**
 * Generic class for roles
 */
class Generic extends Role {

	public function __construct( $name, $permissionRegistry ) {
		$this->name = $name;
		parent::__construct( $permissionRegistry );
	}

	/**
	 * Returns the name of the Role
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string[]
	 */
	public function getRequiredPermissions() {
		return [ 'read' ];
	}
}
