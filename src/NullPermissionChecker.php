<?php

namespace BlueSpice;

use IContextSource;
use User;

class NullPermissionChecker implements IPermissionChecker {

	/**
	 *
	 * @param User $user
	 * @param type $permission
	 * @param IContextSource|null $context
	 * @return bool
	 */
	public function userCan( User $user, $permission, IContextSource $context = null ) {
		return true;
	}

}
