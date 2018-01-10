<?php

namespace BlueSpice\Hook\ResourceLoaderGetConfigVars;

use BlueSpice\Hook\ResourceLoaderGetConfigVars;

class AddGraphicalListRegistry extends ResourceLoaderGetConfigVars {

	protected function doProcess() {
		$this->vars = array_merge(
			$this->vars,
			$this->getSettingsToExpose()
		);
		return true;
	}

	protected function getSettingsToExpose() {
		$registry = \ExtensionRegistry::getInstance();
		$graphicalListRegistry = $registry->getAttribute( 'BlueSpiceFoundationGraphicalListRegistry' );
		return [ 'bsGraphicalListRegistry' => $graphicalListRegistry ];
	}
}
