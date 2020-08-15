<?php

namespace BlueSpice\ResourceModule;

use MWStake\MediaWiki\Component\CommonUserInterface\ResourceLoader\LessVars;

class ExtJS extends LessVars {

	/**
	 *
	 * @param \ResourceLoaderContext|null $context
	 * @return array
	 */
	public function getDependencies( \ResourceLoaderContext $context = null ) {
		$dependencies = [
			'mediawiki.Title',
			'ext.bluespice'
		];

		/**
		 * This is very bad, but as long as https://gerrit.wikimedia.org/r/c/389412/
		 * is not merged CI will crash by an unresolved dependency
		 */
		if ( \ExtensionRegistry::getInstance()->isLoaded( 'ExtJSBase' ) ) {
			$dependencies[] = 'ext.extjsbase.MWExt';
		}

		return $dependencies;
	}
}
