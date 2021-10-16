<?php

namespace BlueSpice\Permission\Role;

class StructureManager extends Role {
	/**
	 * Returns the name of the Role
	 *
	 * @return string
	 */
	public function getName() {
		return "structuremanager";
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
		return 70;
	}
}
