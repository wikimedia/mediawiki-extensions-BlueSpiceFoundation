<?php

namespace BlueSpice\Permission\Role;

class AutoCreateAccount extends Role {
	/**
	 * Returns the name of the Role
	 *
	 * @return string
	 */
	public function getName() {
		return "autocreateaccount";
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
		return 5;
	}
}
