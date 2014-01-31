<?php

class BsGroupHelper {

	protected static $sLockModeGroup = 'lockmode';

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
	 * @global Array $wgGroupPermissions
	 * @param User $oUser
	 * @param String $sGroupName
	 * @param Array $aPermissions
	 * @return boolean alway true - keeps the hook system running
	 */
	public static function addTemporaryGroupToUser( $oUser, $sGroupName, $aPermissions ) {
		global $wgGroupPermissions;

		foreach ( $aPermissions as $sPermission ) {
			$wgGroupPermissions[$sGroupName][$sPermission] = true;
		}

		self::$sTempGroup = $sGroupName;

		$oUser->addGroup( $sGroupName );

		return true;
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

}