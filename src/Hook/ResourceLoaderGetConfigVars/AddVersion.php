<?php

namespace BlueSpice\Hook\ResourceLoaderGetConfigVars;

use BlueSpice\Hook\ResourceLoaderGetConfigVars;
use MediaWiki\Parser\Sanitizer;

class AddVersion extends ResourceLoaderGetConfigVars {

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$this->vars = array_merge(
			$this->vars,
			$this->getSettingsToExpose()
		);
		return true;
	}

	/**
	 *
	 * @return array
	 */
	protected function getSettingsToExpose() {
		$version = '';
		$versionFile = $GLOBALS['IP'] . '/BLUESPICE-VERSION';
		if ( file_exists( $versionFile ) ) {
			$versionFileContent = file_get_contents( $versionFile );
			$version = ' ' . Sanitizer::stripAllTags( $versionFileContent );
		}
		return [ 'bsgVersion' => $version ];
	}
}
