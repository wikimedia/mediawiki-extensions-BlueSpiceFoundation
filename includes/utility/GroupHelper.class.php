<?php

use MediaWiki\MediaWikiServices;

/**
 * DEPRECATED!
 * @deprecated since version 4.1 - use Services->getService( 'BSUtilityFactory' )
 * ->getGroupHelper() instead
 */
class BsGroupHelper {

	/**
	 * DEPRECATED!
	 * @deprecated since version 4.1 - there will be no replacement
	 * @var string
	 */
	protected static $sLockModeGroup = 'lockmode';

	/**
	 * DEPRECATED!
	 * @deprecated since version 4.1 - there will be no replacement
	 * Public getter for lockmode group. This is needed by some extensions.
	 * @return string
	 */
	public static function getLockModeGroup() {
		return self::$sLockModeGroup;
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 4.1 - there will be no replacement
	 * @var string
	 */
	protected static $aGroups = [];

	/**
	 * DEPRECATED!
	 * @deprecated since version 4.1 - use Services->getService( 'BSUtilityFactory' )->getGroupHelper()
	 * ->getAvailableGroups() instead
	 * @param array $aConf
	 * @return array
	 */
	public static function getAvailableGroups( $aConf = [] ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$groupHelper = MediaWikiServices::getInstance()->getService( 'BSUtilityFactory' )->getGroupHelper();
		return $groupHelper->getAvailableGroups( $aConf );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 4.1 - there will be no replacement
	 * @param string $sRight
	 * @param array $aConf
	 * @return array
	 */
	public static function getGroupsByRight( $sRight, $aConf = [] ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
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
	 * DEPRECATED!
	 * @deprecated since version 4.1 - use Services->getService( 'BSUtilityFactory' )->getGroupHelper()
	 * ->getUserInGroups() instead
	 * @param mixed $aGroups
	 * @return array Array of User objects
	 */
	public static function getUserInGroups( $aGroups ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$groupHelper = MediaWikiServices::getInstance()->getService( 'BSUtilityFactory' )->getGroupHelper();
		return $groupHelper->getUserInGroups( $aGroups );
	}

}
