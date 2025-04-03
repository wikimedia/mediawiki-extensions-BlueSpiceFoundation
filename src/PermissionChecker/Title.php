<?php

namespace BlueSpice\PermissionChecker;

use MediaWiki\Context\IContextSource;
use MediaWiki\MediaWikiServices;
use MediaWiki\User\User;

class Title implements \BlueSpice\IPermissionChecker {

	/**
	 *
	 * @param User $user
	 * @param type $permission
	 * @param IContextSource|null $context
	 * @return bool
	 */
	public function userCan( User $user, $permission, ?IContextSource $context = null ) {
		$pm = MediaWikiServices::getInstance()->getPermissionManager();
		if ( !$context->getTitle() ) {
			return $pm->userHasRight( $user, $permission );
		}
		return $pm->userCan( $permission, $user, $context->getTitle() );
	}
}
