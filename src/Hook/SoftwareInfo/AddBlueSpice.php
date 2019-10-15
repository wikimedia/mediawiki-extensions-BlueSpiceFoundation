<?php

namespace BlueSpice\Hook\SoftwareInfo;

use SpecialPage;

class AddBlueSpice extends \BlueSpice\Hook\SoftwareInfo {

	protected static $configName = 'bsg';

	protected function doProcess() {
		$extInfo = $this->getConfig()->get( 'BlueSpiceExtInfo' );

		$link = SpecialPage::getTitleFor( 'SpecialCredits' )->getFullURL();
		$this->softwareInfo["[https://bluespice.com/{$extInfo['name']}] ([$link Credits])"]
			= $extInfo['version'];
		return true;
	}
}
