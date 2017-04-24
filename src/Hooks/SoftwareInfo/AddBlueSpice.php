<?php

namespace BlueSpice\Hooks\SoftwareInfo;

class AddBlueSpice extends \BlueSpice\Hooks\SoftwareInfo {

	protected static $configName = 'bsg';

	protected function doProcess() {
		$extInfo = $this->getConfig()->get( 'BlueSpiceExtInfo' );

		$this->softwareInfo['[http://bluespice.com/ ' .  $extInfo['name'] . '] ([' . \SpecialPage::getTitleFor( 'SpecialCredits' )->getFullURL() . ' Credits])'] = $extInfo['version'];
		return true;
	}
}

