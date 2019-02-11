<?php

namespace BlueSpice\Utility\WikiTextLinksHelper;

class InterlanguageLinksHelper extends InterwikiLinksHelper {

	/**
	 *
	 * @param \Title|null $title
	 * @return bool
	 */
	protected function isValidInterwikiLink( \Title $title = null ) {
		if ( !parent::isValidInterwikiLink( $title ) ) {
			return false;
		}
		return \Language::isValidCode( $title->getInterwiki() );
	}
}
