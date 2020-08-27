<?php

namespace BlueSpice\Tests\Utility\WikiTextLinksHelper;

use BlueSpice\Utility\WikiTextLinksHelper\InterlanguageLinksHelper;
use MediaWiki\MediaWikiServices;

class InterlanguageLinksHelperTest extends InterwikiLinksHelperTest {

	/**
	 *
	 * @return array
	 */
	protected function getExpected() {
		return $this->provideInterlanguageLinks();
	}

	/**
	 *
	 * @param string $wikitext
	 * @return InterlanguageLinksHelper
	 */
	protected function getHelper( $wikitext ) {
		return new InterlanguageLinksHelper( $wikitext, MediaWikiServices::getInstance() );
	}
}
