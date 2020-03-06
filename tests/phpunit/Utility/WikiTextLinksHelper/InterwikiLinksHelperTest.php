<?php

namespace BlueSpice\Tests\Utility\WikiTextLinksHelper;

use BlueSpice\Utility\WikiTextLinksHelper\InterwikiLinksHelper;
use MediaWiki\MediaWikiServices;

class InterwikiLinksHelperTest extends InternalLinksHelperTest {

	/**
	 *
	 * @return array
	 */
	protected function getExpected() {
		return $this->provideInterwikiLinks() + $this->provideInterlanguageLinks();
	}

	/**
	 *
	 * @param string $wikitext
	 * @return InterwikiLinksHelper
	 */
	protected function getHelper( $wikitext ) {
		return new InterwikiLinksHelper( $wikitext, MediaWikiServices::getInstance() );
	}
}
