<?php

namespace BlueSpice\Hook\SetupAfterCache;

class AddParamDefinitions extends \BlueSpice\Hook\SetupAfterCache {

	protected function doProcess() {
		if ( !isset( $GLOBALS['wgParamDefinitions'] ) ) {
			$GLOBALS['wgParamDefinitions'] = [];
		}
		$GLOBALS['wgParamDefinitions'] += [
			'titlelist' => [
				'definition' => 'BSTitleListParam',
				// TODO: Find way to define parser and validator in definition
				// class rather than in global registration
				'string-parser' => 'BSTitleParser'
			],
			'namespacelist' => [
				'definition' => 'BSNamespaceListParam',
				'string-parser' => 'BSNamespaceParser'
			],
			'categorylist' => [
				'definition' => 'BSCategoryListParam',
				'string-parser' => 'BSCategoryParser'
			]
		];

		return true;
	}

}
