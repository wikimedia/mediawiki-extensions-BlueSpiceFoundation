<?php

namespace BlueSpice\Hook\HtmlPageLinkRendererEnd;

use MediaWiki\Title\Title;

class AddDataTitle extends \BlueSpice\Hook\HtmlPageLinkRendererEnd {

	protected function skipProcessing() {
		// this is a bit hacky but without the parser test for extension cite
		// may fail, as it checks for the equality of the complete parserd html
		// string, we modify here. We use our own test to verify that this code
		// works
		if (
			( defined( 'MW_PHPUNIT_TEST' ) && !isset( $GLOBALS[ 'BS_ADD_DATA_TITLE_TEST' ] ) ) ||
			( defined( 'MW_QUIBBLE_CI' ) && !isset( $GLOBALS[ 'BS_ADD_DATA_TITLE_TEST' ] ) )
		) {
			return true;
		}
		if ( $this->target->isExternal() ) {
			return true;
		}
		if ( empty( $this->target->getDBkey() ) ) {
			// not a real target (i.e links from the cite extension)
			return true;
		}
		return false;
	}

	protected function doProcess() {
		// We add the original title to a link. This may be the same content as
		// "title" attribute, but it doesn't have to. I.e. in red links
		$title = Title::newFromLinkTarget( $this->target );
		if ( !$title->exists() && !$title->canExist() ) {
			// Invalid title
			return true;
		}
		$this->attribs['data-bs-title'] = $title->getPrefixedDBkey();

		if ( $this->target->getNamespace() === NS_FILE ) {
			$this->attribs['data-bs-filename'] = $this->target->getText();
			$this->attribs['data-bs-filetimestamp'] = '';
		}

		return true;
	}
}
