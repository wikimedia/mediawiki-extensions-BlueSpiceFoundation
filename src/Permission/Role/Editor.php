<?php

namespace BlueSpice\Permission\Role;

class Editor extends Role {
	/**
	 * Returns the name of the Role
	 *
	 * @return string
	 */
	public function getName() {
		return "editor";
	}

	/**
	 * @return string[]
	 */
	public function getRequiredPermissions() {
		return [ 'read', 'edit' ];
	}
}
