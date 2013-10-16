<?php

class BsGroupHelper {
	protected static $sLockModeGroup = 'lockmode';
	protected static $aGroups = array();
	
	public static function getAvailableGroups( $aConf = array() ) {
		$aBlacklist = array();
		if( isset( $aConf['blacklist'] ) ) {
			if( !is_array( $aConf['blacklist'] ) ) $aConf['blacklist'] = (array) $aConf['blacklist'];
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
		if( isset( $aConf['blacklist'] ) ) {
			if( !is_array( $aConf['blacklist'] ) ) $aConf['blacklist'] = (array) $aConf['blacklist'];
			$aBlacklist = $aConf['blacklist'];
		}
		
		$aGroups = array();
		foreach ( $wgGroupPermissions as $sGroup => $aPermissions ) {
			if( in_array( $sGroup, $aBlacklist ) ) continue;
			foreach( $aPermissions as $sPermissionName => $bBool ) {
				if( $sPermissionName == $sRight ) {
					$aGroups[] = $sGroup;
				}
			}
		}
		
		return $aGroups;
	}
	
}