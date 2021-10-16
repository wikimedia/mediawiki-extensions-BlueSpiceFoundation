<?php

namespace BlueSpice\Permission\Role;

class Author extends Role {
	/**
	 * Returns the name of the Role
	 *
	 * @return string
	 */
	public function getName() {
		return "author";
	}

	/**
	 * @return string[]
	 */
	public function getRequiredPermissions() {
		return [ 'read', 'edit' ];
	}

	/**
	 * @return int
	 */
	public function getPrivilegeLevel() {
		return 40;
	}
}
