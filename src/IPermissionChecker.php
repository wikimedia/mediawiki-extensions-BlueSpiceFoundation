<?php

namespace BlueSpice;

use MediaWiki\Context\IContextSource;
use MediaWiki\User\User;

interface IPermissionChecker {

	/**
	 *
	 * @param User $user
	 * @param type $permission
	 * @param IContextSource|null $context
	 * @return bool
	 */
	public function userCan( User $user, $permission, ?IContextSource $context = null );

}
