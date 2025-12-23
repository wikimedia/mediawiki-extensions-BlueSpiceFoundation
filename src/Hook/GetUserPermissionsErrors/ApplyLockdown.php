<?php

namespace BlueSpice\Hook\GetUserPermissionsErrors;

use BlueSpice\Permission\Lockdown;
use MediaWiki\Message\Message;

class ApplyLockdown extends \BlueSpice\Hook\GetUserPermissionsErrors {

	protected function skipProcessing() {
		return !$this->getLockdown()->isLockedDown( $this->action );
	}

	/**
	 * Checks if requested action belongs to a role
	 * that is explicitly granted only to some groups
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$res = $this->getLockdown()->getLockState( $this->action );

		if ( !$res->isOK() ) {
			if ( is_string( $this->result ) ) {
				if ( empty( $this->result ) ) {
					$this->result = [];
				} else {
					$this->result = [ $this->result ];
				}
			}
			$errors = $res->getMessages( 'error' );
			foreach ( $errors as $error ) {
				$this->result[] = Message::newFromSpecifier( $error )->text();
			}
		}

		return false;
	}

	/**
	 *
	 * @return Lockdown
	 */
	protected function getLockdown() {
		return $this->getServices()->getService( 'BSPermissionLockdownFactory' )
			->newFromTitleAndUserRelation( $this->title, $this->user );
	}

}
