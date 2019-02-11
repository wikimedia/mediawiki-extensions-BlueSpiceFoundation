<?php

namespace BlueSpice\PermissionChecker;

use User;
use IContextSource;

class Title implements \BlueSpice\IPermissionChecker {

	/**
	 *
	 * @param User $user
	 * @param type $permission
	 * @param IContextSource|null $context
	 * @return bool
	 */
	public function userCan( User $user, $permission, IContextSource $context = null ) {
		if ( !$context->getTitle() ) {
			return $user->isAllowed( $permission );
		}
		return $context->getTitle()->userCan( $permission, $user );
	}
}
