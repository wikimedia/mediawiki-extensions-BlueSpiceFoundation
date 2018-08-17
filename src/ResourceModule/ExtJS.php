<?php

namespace BlueSpice\ResourceModule;

class ExtJS extends \ResourceLoaderFileModule {
	public function getDependencies( \ResourceLoaderContext $context = null ) {
		$dependencies = [
			'mediawiki.Title',
			'ext.bluespice'
		];

		/**
		 * This is very bad, but as long as https://gerrit.wikimedia.org/r/c/389412/
		 * is not merged CI will crash by an unresolved dependency
		 */
		if( \ExtensionRegistry::getInstance()->isLoaded( 'ExtJSBase' ) ) {
			$dependencies[] = 'ext.extjsbase.MWExt';
		}

		return $dependencies;
	}

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
