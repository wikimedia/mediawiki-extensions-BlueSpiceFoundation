<?php

namespace BlueSpice\Hook\ResourceLoaderGetConfigVars;

use BlueSpice\Hook\ResourceLoaderGetConfigVars;

class AddVersion extends ResourceLoaderGetConfigVars {

	protected function doProcess() {
		$this->vars = array_merge(
			$this->vars,
			$this->getSettingsToExpose()
		);
		return true;
	}

	protected function getSettingsToExpose() {
		$extInfo = $this->getConfig()->get( 'BlueSpiceExtInfo' );
		return [ 'bsgVersion' => $extInfo["version"] ];
	}
}
