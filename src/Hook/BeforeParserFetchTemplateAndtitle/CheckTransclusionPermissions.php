<?php

namespace BlueSpice\Hook\BeforeParserFetchTemplateAndtitle;

use BlueSpice\Hook\BeforeParserFetchTemplateAndtitle;

class CheckTransclusionPermissions extends BeforeParserFetchTemplateAndtitle {

	/**
	 * Check if user can read the page that is being transcluded
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$user = $this->parser->getUser();
		$mwPermissionManager = $this->getServices()->getPermissionManager();

		if ( $mwPermissionManager->userCan( 'read', $user, $this->title ) ) {
			$this->skip = true;
			return false;
		}

		return true;
	}
}
