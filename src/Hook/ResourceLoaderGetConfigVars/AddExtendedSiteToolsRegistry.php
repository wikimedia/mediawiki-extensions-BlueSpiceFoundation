<?php

namespace BlueSpice\Hook\ResourceLoaderGetConfigVars;

use BlueSpice\Hook\ResourceLoaderGetConfigVars;

class AddExtendedSiteToolsRegistry extends ResourceLoaderGetConfigVars {

	protected function doProcess() {
		$this->vars = array_merge(
			$this->vars,
			$this->getSettingsToExpose()
		);
		return true;
	}

	protected function getSettingsToExpose() {
		$registry = \ExtensionRegistry::getInstance();
		$extendedSiteToolsRegistry = $registry->getAttribute( 'BlueSpiceFoundationExtendedSiteToolsRegistry' );
		return [ 'bsExtendedSiteToolsRegistry' => $extendedSiteToolsRegistry ];
	}
}
