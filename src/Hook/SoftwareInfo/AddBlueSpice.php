<?php

namespace BlueSpice\Hook\SoftwareInfo;

use MediaWiki\Parser\Sanitizer;
use MediaWiki\SpecialPage\SpecialPage;

class AddBlueSpice extends \BlueSpice\Hook\SoftwareInfo {

	protected static $configName = 'bsg';

	protected function doProcess() {
		$version = '';
		$versionFile = $GLOBALS['IP'] . '/BLUESPICE-VERSION';
		if ( file_exists( $versionFile ) ) {
			$versionFileContent = file_get_contents( $versionFile );
			$version = ' ' . Sanitizer::stripAllTags( $versionFileContent );
		}

		if ( empty( $version ) ) {
			return true;
		}

		$buildInfo = '';
		$buildInfoFile = $GLOBALS['IP'] . '/BUILDINFO';
		if ( file_exists( $buildInfoFile ) ) {
			$buildInfoFileContent = file_get_contents( $buildInfoFile );
			$buildInfo = ' (build:' . Sanitizer::stripAllTags( $buildInfoFileContent ) . ')';
		}

		$edition = '';
		$editionFile = $GLOBALS['IP'] . '/BLUESPICE-EDITION';
		if ( file_exists( $editionFile ) ) {
			$editionFileContent = file_get_contents( $editionFile );
			$edition = ' ' . Sanitizer::stripAllTags( $editionFileContent );
		}

		$name = "BlueSpice$edition";

		$link = SpecialPage::getTitleFor( 'SpecialCredits' )->getFullURL();
		$this->softwareInfo["[https://bluespice.com $name] ([$link Credits])"]
			= $version . $buildInfo;
		return true;
	}
}
