<?php

namespace BlueSpice\Hook\SoftwareInfo;

use MediaWiki\Parser\Sanitizer;
use MediaWiki\SpecialPage\SpecialPage;

class AddBlueSpice extends \BlueSpice\Hook\SoftwareInfo {

	/** @var string */
	protected static $configName = 'bsg';

	/**
	 * @return bool
	 */
	protected function doProcess() {
		$version = '';
		$versionFile = $GLOBALS['IP'] . '/BLUESPICE-VERSION';
		if ( file_exists( $versionFile ) ) {
			$versionFileContent = file_get_contents( $versionFile );
			$version = Sanitizer::stripAllTags( $versionFileContent );
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

		$edition = $this->getEditionFromEnv();
		if ( $edition === '' ) {
			$editionFile = $GLOBALS['IP'] . '/BLUESPICE-EDITION';
			if ( file_exists( $editionFile ) ) {
				$editionFileContent = file_get_contents( $editionFile );
				$edition = ' ' . Sanitizer::stripAllTags( $editionFileContent );
			}
		}

		$name = "BlueSpice$edition";

		$link = SpecialPage::getTitleFor( 'SpecialCredits' )->getFullURL();
		$this->softwareInfo["[https://bluespice.com $name] ([$link Credits])"]
			= $version . $buildInfo;
		return true;
	}

	/**
	 * Builds " <Product> <Edition>" from the `INTERNAL_PRODUCT` and `INTERNAL_EDITION`
	 * environment variables. Returns an empty string if neither is set.
	 *
	 * @return string
	 */
	private function getEditionFromEnv(): string {
		$parts = [];
		foreach ( [ 'INTERNAL_PRODUCT', 'INTERNAL_EDITION' ] as $varName ) {
			$value = getenv( $varName );
			if ( !is_string( $value ) ) {
				continue;
			}
			$value = trim( Sanitizer::stripAllTags( $value ) );
			if ( $value === '' ) {
				continue;
			}
			$parts[] = ucfirst( $value );
		}
		if ( empty( $parts ) ) {
			return '';
		}
		return ' ' . implode( ' ', $parts );
	}
}
