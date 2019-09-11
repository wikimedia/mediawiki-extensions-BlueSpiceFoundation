<?php

namespace BlueSpice\Hook\SetupAfterCache;

class AddConfigFiles extends \BlueSpice\Hook\SetupAfterCache {

	protected function doProcess() {
		// phpcs:ignore MediaWiki.VariableAnalysis.UnusedGlobalVariables
		global $wgExtensionFunctions, $wgGroupPermissions, $wgWhitelistRead, $wgMaxUploadSize;
		// phpcs:ignore MediaWiki.VariableAnalysis.UnusedGlobalVariables
		global $wgNamespacePermissionLockdown, $wgSpecialPageLockdown, $wgActionLockdown, $wgNonincludableNamespaces;
		// phpcs:ignore MediaWiki.VariableAnalysis.UnusedGlobalVariables
		global $wgExtraNamespaces, $wgContentNamespaces, $wgNamespacesWithSubpages, $wgNamespacesToBeSearchedDefault;
		// phpcs:ignore MediaWiki.VariableAnalysis.UnusedGlobalVariables
		global $wgLocalisationCacheConf, $wgAutoloadLocalClasses, $wgFlaggedRevsNamespaces, $wgNamespaceAliases, $wgVersion;
		/*
		 * TODO: All those globals above can be removed once all included
		 * settings files use $GLOBALS['wg...'] to access them
		 */
		foreach ( $this->getConfig()->get( 'ConfigFiles' ) as $sConfigFileKey => $sConfigFilePath ) {
			if ( file_exists( $sConfigFilePath ) ) {
				include $sConfigFilePath;
			}
		}

		return true;
	}

}
