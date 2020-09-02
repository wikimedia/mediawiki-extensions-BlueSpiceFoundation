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

	protected static $aGroups = [];

	/**
	 *
	 * @param array $aConf
	 * @return array
	 */
	public static function getAvailableGroups( $aConf = [] ) {
		$aBlacklist = [];

		if ( isset( $aConf['blacklist'] ) ) {
			if ( !is_array( $aConf['blacklist'] ) ) {
				$aConf['blacklist'] = (array)$aConf['blacklist'];
			}
			$aBlacklist = $aConf['blacklist'];
		}

		$aBlacklist[] = self::$sLockModeGroup;

		$bDoReload = false;
		if ( isset( $aConf['reload'] ) ) {
			$bDoReload = $aConf['reload'];
		}
		if ( empty( self::$aGroups ) ) {
			$bDoReload = true;
		}

		if ( $bDoReload ) {
			self::$aGroups = array_merge(
				User::getImplicitGroups(),
				User::getAllGroups()
			);
			self::$aGroups = array_diff( self::$aGroups, $aBlacklist );
			natsort( self::$aGroups );
		}

		return self::$aGroups;
	}

	/**
	 *
	 * @param string $sRight
	 * @param array $aConf
	 * @return array
	 */
	public static function getGroupsByRight( $sRight, $aConf = [] ) {
		global $wgGroupPermissions;
		$aBlacklist = [];

		if ( isset( $aConf['blacklist'] ) ) {
			if ( !is_array( $aConf['blacklist'] ) ) {
				$aConf['blacklist'] = (array)$aConf['blacklist'];
			}
			$aBlacklist = $aConf['blacklist'];
		}

		$aGroups = [];
		foreach ( $wgGroupPermissions as $sGroup => $aPermissions ) {
			if ( in_array( $sGroup, $aBlacklist ) ) {
				continue;
			}
			foreach ( $aPermissions as $sPermissionName => $bBool ) {
				if ( $sPermissionName == $sRight ) {
					$aGroups[] = $sGroup;
				}
			}
		}

		return $aGroups;
	}

	/**
	 * Returns an array of User being in one or all groups given
	 * @param mixed $aGroups
	 * @return array Array of User objects
	 */
	public static function getUserInGroups( $aGroups ) {
		$dbr = wfGetDB( DB_REPLICA );
		if ( !is_array( $aGroups ) ) {
			$aGroups = [ $aGroups ];
		}
		$aUser = [];
		$res = $dbr->select(
			'user_groups',
			[ 'ug_user' ],
			[ 'ug_group' => $aGroups ],
			__METHOD__,
			[ 'DISTINCT' ]
			);
		if ( !$res ) {
			return $aUser;
		}
		foreach ( $res as $row ) {
			$aUser [] = User::newFromId( $row->ug_user );
		}
		return $aUser;
	}

}
