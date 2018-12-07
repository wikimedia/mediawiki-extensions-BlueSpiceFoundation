<?php

namespace BlueSpice\ResourceModule;

class LessVars extends \ResourceLoaderFileModule {
	public function getLessVars(\ResourceLoaderContext $context) {
		$vars = parent::getLessVars( $context );
		$registry = new \BlueSpice\ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationLessVarsRegistry'
		);
		foreach( $registry->getAllKeys() as $key ) {
			$vars[$key] = $registry->getValue( $key, '¯\_(ツ)_/¯' );
		}

		// TODO: make LessVars text area field with valiadtion for ConfigManager
		return array_merge(
			$vars,
			$this->getConfig()->get( 'LessVars' )
		);
	}

	/**
	 * @return Config
	 * @since 1.24
	 */
	public function getConfig() {
		return \BlueSpice\Services::getInstance()->getConfigFactory()
			->makeConfig( 'bsg' );
	}
}
