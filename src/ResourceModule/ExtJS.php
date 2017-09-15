<?php

namespace BlueSpice\ResourceModule;

class ExtJS extends \ResourceLoaderFileModule {
	public function getDependencies( \ResourceLoaderContext $context = null ) {
		$dependencies = [
			'mediawiki.Title'
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
}
