<?php

namespace BlueSpice\Hook\SetupAfterCache;

class AddParamDefinitions extends \BlueSpice\Hook\SetupAfterCache {

	protected function doProcess() {
		if ( !isset( $GLOBALS['wgParamDefinitions'] ) ) {
			$GLOBALS['wgParamDefinitions'] = [];
		}
		$GLOBALS['wgParamDefinitions'] += array(
			'titlelist' => array(
				'definition' => 'BSTitleListParam',
				//TODO: Find way to define parser and validator in definition
				//class rather than in global registration
				'string-parser' => 'BSTitleParser'
			),
			'namespacelist' => array(
				'definition' => 'BSNamespaceListParam',
				'string-parser' => 'BSNamespaceParser'
			),
			'categorylist' => array(
				'definition' => 'BSCategoryListParam',
				'string-parser' => 'BSCategoryParser'
			)
		);

		return true;
	}

}
