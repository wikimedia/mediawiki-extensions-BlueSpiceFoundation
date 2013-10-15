<?php

class BsGroupHelper {
	public static $sLockModeGroup = 'lockmode';
	
	public static function getAvailableGroups( $aConf = array() ) {
		global $wgGroupPermissions;
		$aBlacklist = array();
		if( isset( $aConf['blacklist'] ) ) {
			if( !is_array( $aConf['blacklist'] ) ) $aConf['blacklist'] = (array) $aConf['blacklist'];
			$aBlacklist = $aConf['blacklist'];
		}
		$aBlacklist[] = self::$sLockModeGroup;

		$aGroups = array();
		foreach ( $wgGroupPermissions as $sGroup => $aPermissions ) {
			if( in_array( $sGroup, $aBlacklist ) ) continue;
			$aGroups[] = $sGroup;
		}
		
		return $aGroups;
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