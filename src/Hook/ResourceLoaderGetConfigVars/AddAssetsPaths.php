<?php

namespace BlueSpice\Hook\ResourceLoaderGetConfigVars;

use BlueSpice\Hook\ResourceLoaderGetConfigVars;

class AddAssetsPaths extends ResourceLoaderGetConfigVars {

	protected function doProcess() {
		$this->vars = array_merge(
			$this->vars,
			$this->getSettingsToExpose()
		);
		return true;
	}

	protected function getSettingsToExpose() {
		$extensionAssetsPath = $this->getConfig()->get( 'ExtensionAssetsPath' );
		$registry = $this->getServices()->getService(
			'BSExtensionRegistry'
		);
		$definitions = $registry->getExtensionDefinitions();
		$paths = [];

		foreach( $definitions as $sName => $definition ) {
			$paths[$sName] = $extensionAssetsPath.$definition['extPath'];
		}

		return ['bsExtensionManagerAssetsPaths' => $paths ];
	}
}
