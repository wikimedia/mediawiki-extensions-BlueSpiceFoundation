<?php

namespace BlueSpice\Permission\Role;

class Admin extends Role {
	/**
	 * Returns the name of the Role
	 *
	 * @return string
	 */
	function getName() {
		return "admin";
	}
}
