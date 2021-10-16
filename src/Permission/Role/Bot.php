<?php

namespace BlueSpice\Permission\Role;

class Bot extends Role {
	/**
	 * Returns the name of the Role
	 *
	 * @return string
	 */
	public function getName() {
		return "bot";
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
		return 100;
	}
}
