<?php

namespace BlueSpice\Permission\Role;

class Reviewer extends Role {
	/**
	 * Returns the name of the Role
	 *
	 * @return string
	 */
	function getName() {
		return "reviewer";
	}
}
