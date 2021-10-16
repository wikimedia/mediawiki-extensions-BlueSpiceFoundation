<?php

namespace BlueSpice\Permission\Role;

class MaintenanceAdmin extends Role {
	/**
	 * Returns the name of the Role
	 *
	 * @return string
	 */
	public function getName() {
		return "maintenanceadmin";
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
		return 110;
	}
}
