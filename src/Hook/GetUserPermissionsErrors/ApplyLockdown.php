<?php

namespace BlueSpice\Hook\GetUserPermissionsErrors;

use BlueSpice\Permission\Lockdown;

class ApplyLockdown extends \BlueSpice\Hook\GetUserPermissionsErrors {

	protected function skipProcessing() {
		return !$this->getLockdown()->isLockedDown( $this->action );
	}

	/**
	 * Checks if requested action belongs to a role
	 * that is explicitly granted only to some groups
	 *
	 * @return boolean
	 */
	protected function doProcess () {
		$this->result = $this->getLockdown()->getLockState( $this->action )->getMessage();

		return false;
	}

	/**
	 *
	 * @return Lockdown
	 */
	protected function getLockdown() {
		return $this->getServices()->getBSPermissionLockdownFactory()
			->newFromTitleAndUserRelation( $this->title, $this->user );
	}

}
