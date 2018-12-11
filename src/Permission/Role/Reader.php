<?php

namespace BlueSpice\Permission\Role;

class Reader extends Role {
	/**
	 * Returns the name of the Role
	 *
	 * @return string
	 */
	function getName() {
		return "reader";
	}
}
