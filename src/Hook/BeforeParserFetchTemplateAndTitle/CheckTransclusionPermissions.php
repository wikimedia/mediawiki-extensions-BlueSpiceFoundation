<?php

namespace BlueSpice\Hook\BeforeParserFetchTemplateAndTitle;

use BlueSpice\Hook\BeforeParserFetchTemplateAndTitle;
use MediaWiki\User\UserIdentity;
use RequestContext;

class CheckTransclusionPermissions extends BeforeParserFetchTemplateAndTitle {

	/**
	 * Check if user can read the page that is being transcluded
	 *
	 * @return bool
	 */
	protected function doProcess() {
		if ( defined( 'MW_NO_SESSION' ) ) {
			// Bail out on no session entry points, since we cannot init user
			return true;
		}

		$user = $this->parser->getUserIdentity();
		if ( !$user instanceof UserIdentity || !$user->isRegistered() ) {
			$user = RequestContext::getMain()->getUser();
		}
		if ( $this->getConfig()->get( 'CommandLineMode' ) ) {
			if ( !$user->isRegistered() ) {
				$user = $this->getServices()->getService( 'BSUtilityFactory' )
					->getMaintenanceUser()->getUser();
			}
		}

		$mwPermissionManager = $this->getServices()->getPermissionManager();
		if ( $mwPermissionManager->userCan( 'read', $user, $this->title ) ) {
			$this->skip = true;
			return false;
		}

		return true;
	}
}
