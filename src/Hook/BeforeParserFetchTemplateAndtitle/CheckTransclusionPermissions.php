<?php

namespace BlueSpice\Hook\BeforeParserFetchTemplateAndtitle;

use BlueSpice\Hook\BeforeParserFetchTemplateAndtitle;
use RequestContext;
use User;

class CheckTransclusionPermissions extends BeforeParserFetchTemplateAndtitle {

	/**
	 * Check if user can read the page that is being transcluded
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$user = $this->parser->getUser();
		if ( !$user instanceof User || !$user->isRegistered() ) {
			$user = RequestContext::getMain()->getUser();
		}
		if ( $this->getConfig()->get( 'CommandLineMode' ) ) {
			if ( $user->isAnon() ) {
				$user = $this->getServices()->getService( 'BSUtilityFactory' )
					->getMaintenanceUser()->getUser();
			}
		}

		if ( !$this->title->userCan( 'read', $user ) ) {
			$this->skip = true;
			return false;
		}

		return true;
	}
}
