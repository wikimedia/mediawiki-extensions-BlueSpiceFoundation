<?php

namespace BlueSpice\Tests\Utility\WikiTextLinksHelper;

use BlueSpice\Utility\WikiTextLinksHelper\CategoryLinksHelper;

class CategoryLinksHelperTest extends InternalLinksHelperTest {

	/**
	 *
	 * @return array
	 */
	protected function getExpected() {
		return $this->provideCategoryLinks();
	}

	/**
	 *
	 * @param string $wikitext
	 * @return CategoryLinksHelper
	 */
	protected function getHelper( $wikitext ) {
		return new CategoryLinksHelper( $wikitext );
	}
}
