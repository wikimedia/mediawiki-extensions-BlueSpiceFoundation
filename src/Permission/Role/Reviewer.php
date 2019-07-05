<?php

namespace BlueSpice\Permission\Role;

class Reviewer extends Role {
	/**
	 * Returns the name of the Role
	 *
	 * @return string
	 */
	public function getName() {
		return "reviewer";
	}

	/**
	 * @return string[]
	 */
	public function getRequiredPermissions() {
		return [ 'read' ];
	}
}
