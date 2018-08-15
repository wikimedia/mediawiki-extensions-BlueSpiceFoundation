<?php

namespace BlueSpice\Hook\SetupAfterCache;

class AddConfigFiles extends \BlueSpice\Hook\SetupAfterCache {

	protected function doProcess() {
		global $wgExtensionFunctions, $wgGroupPermissions, $wgWhitelistRead, $wgMaxUploadSize,
		$wgNamespacePermissionLockdown, $wgSpecialPageLockdown, $wgActionLockdown, $wgNonincludableNamespaces,
		$wgExtraNamespaces, $wgContentNamespaces, $wgNamespacesWithSubpages, $wgNamespacesToBeSearchedDefault,
		$wgLocalisationCacheConf, $wgAutoloadLocalClasses, $wgFlaggedRevsNamespaces, $wgNamespaceAliases, $wgVersion;
		/*
		 * TODO: All those globals above can be removed once all included
		 * settings files use $GLOBALS['wg...'] to access them
		 */

		global $bsgConfigFiles;
		foreach( $bsgConfigFiles as $sConfigFileKey => $sConfigFilePath ) {
			if ( file_exists( $sConfigFilePath ) ) {
				include( $sConfigFilePath );
			}
		}

		return true;
	}

}
