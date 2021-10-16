<?php

namespace BlueSpice\Permission\Role;

class Admin extends Role {
	/**
	 * Returns the name of the Role
	 *
	 * @return string
	 */
	public function getName() {
		return "admin";
	}

	/**
	 * @return string[]
	 */
	public function getRequiredPermissions() {
		return [ 'read' ];
	}

	/**
	 * @return int
	 */
	public function getPrivilegeLevel() {
		return 90;
	}
}
