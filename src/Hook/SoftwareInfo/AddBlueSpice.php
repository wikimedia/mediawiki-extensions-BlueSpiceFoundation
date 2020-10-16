<?php

namespace BlueSpice\Hook\SoftwareInfo;

use Sanitizer;
use SpecialPage;

class AddBlueSpice extends \BlueSpice\Hook\SoftwareInfo {

	protected static $configName = 'bsg';

	protected function doProcess() {
		$extInfo = $this->getConfig()->get( 'BlueSpiceExtInfo' );

		$buildInfo = '';
		$buildInfoFile = $GLOBALS['IP'] . '/BUILDINFO';
		if ( file_exists( $buildInfoFile ) ) {
			$buildInfoFileContent = file_get_contents( $buildInfoFile );
			$buildInfo = ' (build:' . Sanitizer::stripAllTags( $buildInfoFileContent ) . ')';
		}

		$link = SpecialPage::getTitleFor( 'SpecialCredits' )->getFullURL();
		$this->softwareInfo["[https://bluespice.com {$extInfo['name']}] ([$link Credits])"]
			= $extInfo['version'] . $buildInfo;
		return true;
	}
}
