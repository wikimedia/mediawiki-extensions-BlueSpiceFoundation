<?php

namespace BlueSpice\Hook\ResourceLoaderGetConfigVars;

use BlueSpice\Hook\ResourceLoaderGetConfigVars;
use MediaWiki\Registration\ExtensionRegistry;

class AddExtendedSiteToolsRegistry extends ResourceLoaderGetConfigVars {

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
		$registry = ExtensionRegistry::getInstance();
		$extendedSiteToolsRegistry = $registry->getAttribute(
			'BlueSpiceFoundationExtendedSiteToolsRegistry'
		);
		return [ 'bsExtendedSiteToolsRegistry' => $extendedSiteToolsRegistry ];
	}
}
