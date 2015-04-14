<?php

class BsGroupHelper {

	protected static $sLockModeGroup = 'lockmode';
	
	/**
	 * Public getter for lockmode group. This is needed by some extensions.
	 * @return string
	 */
	public static function getLockModeGroup() {
		return self::$sLockModeGroup;
	}

	protected static $aGroups = array();

	private static $sTempGroup = '';

	public static function getAvailableGroups( $aConf = array() ) {
		$aBlacklist = array();

		if ( isset( $aConf['blacklist'] ) ) {
			if ( !is_array( $aConf['blacklist'] ) ) $aConf['blacklist'] = (array) $aConf['blacklist'];
			$aBlacklist = $aConf['blacklist'];
		}

		$aBlacklist[] = self::$sLockModeGroup;
		if ( empty( self::$aGroups ) ) {
			self::$aGroups = array_merge(
				User::getImplicitGroups(),
				User::getAllGroups()
			);
			self::$aGroups = array_diff( self::$aGroups, $aBlacklist );
			natsort( self::$aGroups );
		}

		return self::$aGroups;
	}

	public static function getGroupsByRight( $sRight, $aConf = array() ) {
		global $wgGroupPermissions;
		$aBlacklist = array();

		if ( isset( $aConf['blacklist'] ) ) {
			if ( !is_array( $aConf['blacklist'] ) ) $aConf['blacklist'] = (array) $aConf['blacklist'];
			$aBlacklist = $aConf['blacklist'];
		}

		$aGroups = array();
		foreach ( $wgGroupPermissions as $sGroup => $aPermissions ) {
			if ( in_array( $sGroup, $aBlacklist ) ) continue;
			foreach ( $aPermissions as $sPermissionName => $bBool ) {
				if ( $sPermissionName == $sRight ) {
					$aGroups[] = $sGroup;
				}
			}
		}

		return $aGroups;
	}

	/**
	 * @param User $oUser
	 * @param String $sGroupName
	 * @return boolean
	 */
	public static function addTempGroupToUser( $oUser, $sGroupName ) {

		self::$sTempGroup = $sGroupName;
		$oUser->addGroup( $sGroupName );

		return true;
	}

	/**
	 * @global Array $wgGroupPermissions
	 * @param String $sGroupName
	 * @param Array $aPermissions
	 * @param Array $aNamespaces
	 */
	public static function addPermissionsToGroup( $sGroupName, $aPermissions, $aNamespaces = array() ) {
		global $wgGroupPermissions;

		$aNamespaces = array_diff(
			$aNamespaces,
			array( NS_MEDIA, NS_SPECIAL )
		);

		foreach ( $aPermissions as $sPermission ) {
			$wgGroupPermissions[$sGroupName][$sPermission] = true;

			//Check if Lockdown is in use
			if( empty($aNamespaces) || !isset($GLOBALS['wgNamespacePermissionLockdown'])) {
				continue;
			}
			foreach( $aNamespaces as $iNs) {
				if( isset($GLOBALS['wgNamespacePermissionLockdown'][$iNs][$sPermission]) ) {
					if( in_array(
						$sGroupName,
						$GLOBALS['wgNamespacePermissionLockdown'][$iNs][$sPermission]
					)) continue;
				}
				$GLOBALS['wgNamespacePermissionLockdown'][$iNs][$sPermission][]
					= $sGroupName;
			}
		}
	}

	/**
	 * DEPRECATED!
	 * Use GroupHelper::addTempGroupToUser and GroupHelper::addPermissionsToGroup
	 * @deprecated since 2.23.1
	 * @param User $oUser
	 * @param String $sGroupName
	 * @param Array $aPermissions
	 * @param Title $oTitle
	 * @return NULL
	 */
	public static function addTemporaryGroupToUser( $oUser, $sGroupName, $aPermissions, Title $oTitle = null ) {
		//Deprecated, use GroupHelper::addTempGroupToUser and GroupHelper::addPermissionsToGroup

		$aNamespaces = array();
		if( !is_null($oTitle) ) {
			$aNamespaces[] = $oTitle->getNamespace();
		}

		self::addPermissionsToGroup( $sGroupName, $aPermissions, $aNamespaces );
		self::addTempGroupToUser($oUser, $sGroupName);
	}

	/**
	 * Hook-Handler for MediaWiki hook UserAddGroup
	 * @param User $user
	 * @param String $group
	 * @return boolean - returns false to skip saving group into db
	 */
	public static function addTemporaryGroupToUserHelper( $user, &$group ) {
		if ( empty( self::$sTempGroup ) || self::$sTempGroup !== $group ) return true;
		self::$sTempGroup = '';

		return false;
	}

	/**
	 * Returns an array of User being in one or all groups given
	 * @param mixed $aGroups
	 * @return array Array of User objects
	 */
	public static function getUserInGroups( $aGroups ) {
		$dbr = wfGetDB( DB_SLAVE );
		if ( !is_array( $aGroups ) ) {
			$aGroups = array ( $aGroups );
		}
		$aUser = array ();
		$res = $dbr->select(
			'user_groups',
			array ( 'ug_user' ),
			array ( 'ug_group' => $aGroups ),
			__METHOD__,
			array ( 'DISTINCT' )
			);
		if ( !$res ) {
			return $aUser;
		}
		while ( $row = $res->fetchObject() ) {
			$aUser [] = User::newFromId( $row->ug_user );
		}
		return $aUser;
	}

}