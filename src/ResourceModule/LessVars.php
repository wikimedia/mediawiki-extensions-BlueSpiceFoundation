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
		return $vars;
	}
}
