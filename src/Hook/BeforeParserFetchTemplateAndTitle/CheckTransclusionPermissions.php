<?php

namespace BlueSpice\Hook\BeforeParserFetchTemplateAndTitle;

use BlueSpice\Hook\BeforeParserFetchTemplateAndTitle;

class CheckTransclusionPermissions extends BeforeParserFetchTemplateAndTitle {

	/**
	 * Check if user can read the page that is being transcluded
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$user = $this->parser->getUser();
		$mwPermissionManager = $this->getServices()->getPermissionManager();
		if ( $this->getConfig()->get( 'CommandLineMode' ) ) {
			if ( $user->isAnon() ) {
				$user = $this->getServices()->getService( 'BSUtilityFactory' )
					->getMaintenanceUser()->getUser();
			}
		}
		if ( $mwPermissionManager->userCan( 'read', $user, $this->title ) ) {
			$this->skip = true;
			return false;
		}

		return true;
	}
}
