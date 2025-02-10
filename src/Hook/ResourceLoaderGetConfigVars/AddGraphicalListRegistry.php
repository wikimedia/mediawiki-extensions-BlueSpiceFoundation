<?php

namespace BlueSpice\Hook\ResourceLoaderGetConfigVars;

use BlueSpice\Hook\ResourceLoaderGetConfigVars;
use MediaWiki\Registration\ExtensionRegistry;

class AddGraphicalListRegistry extends ResourceLoaderGetConfigVars {

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
		$graphicalListRegistry = $registry->getAttribute( 'BlueSpiceFoundationGraphicalListRegistry' );
		return [ 'bsGraphicalListRegistry' => $graphicalListRegistry ];
	}
}
