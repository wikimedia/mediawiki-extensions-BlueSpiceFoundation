<?php

require_once 'BSMaintenance.php';

class BSCleanUpDeletedUsersBlocks extends LoggedUpdateMaintenance {

	/**
	 *
	 * @return bool
	 */
	protected function doDBUpdates() {
		$dbw = $this->getDB( DB_PRIMARY );
		$users = $dbw->select( 'user', '*' );
		$ipblocks = $dbw->select( 'ipblocks', '*' );
		foreach ( $ipblocks as $blocked ) {
			$found = 0;
			foreach ( $users as $user ) {
				if ( $blocked->ipb_user === $user->user_id ) {
					$found = 1;
				}
			}
			if ( $found === 0 ) {
				echo "Delete blocked user " . $blocked->ipb_user
						. " with no existing user ID. ";
				$dbw->delete( 'ipblocks', [ 'ipb_id' => $blocked->ipb_id ], __METHOD__ );
			}
		}

		return true;
	}

	/**
	 *
	 * @return string
	 */
	protected function getUpdateKey() {
		return 'bs_cleanupdeletedusersblocks';
	}
}
