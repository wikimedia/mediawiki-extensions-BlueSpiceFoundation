<?php

namespace BlueSpice\Hook\ResourceLoaderGetConfigVars;

use BlueSpice\Hook\ResourceLoaderGetConfigVars;

class AddBSGConfig extends ResourceLoaderGetConfigVars {

	protected function doProcess() {
		return true;
	}

	protected function getSettingsToExpose() {
		return [

		];
	}
}