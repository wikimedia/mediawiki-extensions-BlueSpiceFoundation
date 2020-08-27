<?php

namespace BlueSpice\Tests\Utility\WikiTextLinksHelper;

use BlueSpice\Utility\WikiTextLinksHelper\FileLinksHelper;

class FileLinksHelperTest extends InternalLinksHelperTest {

	/**
	 *
	 * @return array
	 */
	protected function getExpected() {
		return $this->provideFileLinks();
	}

	/**
	 *
	 * @param string $wikitext
	 * @return FileLinksHelper
	 */
	protected function getHelper( $wikitext ) {
		return new FileLinksHelper( $wikitext );
	}
}
