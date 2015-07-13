<?php

abstract class BSApiBase extends ApiBase {
	/**
	 * Checks access permissions based on a list of titles and permissions. If
	 * one of it fails the API processing is ended with an appropriate message
	 * @param array $aTitles Array of Title objects to check the requires permissions against
	 * @param User $oUser the User object of the requesting user. Does a fallback to $this->getUser();
	 */
	protected function checkPermissions( $aTitles = array(), $oUser = null ) {
		$aRequiredPermissions = $this->getRequiredPermissions();
		if( empty( $aRequiredPermissions ) ) {
			return; //No need for further checking
		}
		foreach( $aTitles as $oTitle ) {
			if( $oTitle instanceof Title === false ) {
				continue;
			}
			foreach( $aRequiredPermissions as $sPermission ) {
				if( $oTitle->userCan( $sPermission ) === false ) {
					//TODO: Reflect title and permission in error message
					$this->dieUsageMsg( 'badaccess-groups' );
				}
			}
		}

		//Fallback if not conrete title was provided
		if( empty( $aTitles ) ) {
			if( $oUser instanceof User === false ) {
				$oUser = $this->getUser();
			}
			foreach( $aRequiredPermissions as $sPermission ) {
				if( $oUser->isAllowed( $sPermission ) === false ) {
					//TODO: Reflect permission in error message
					$this->dieUsageMsg( 'badaccess-groups' );
				}
			}
		}
	}

	protected function getRequiredPermissions() {
		return array( 'read' );
	}

	protected function getExamples() {
		return array(
			'api.php?action='.$this->getModuleName(),
		);
	}
}