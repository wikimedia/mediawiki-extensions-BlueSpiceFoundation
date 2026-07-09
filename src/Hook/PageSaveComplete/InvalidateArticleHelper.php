<?php

namespace BlueSpice\Hook\PageSaveComplete;

use BlueSpice\Hook\PageSaveComplete;

class InvalidateArticleHelper extends PageSaveComplete {

	/**
	 * @return bool
	 */
	protected function skipProcessing() {
		if ( !$this->wikiPage->getTitle()->exists() ) {
			return true;
		}

		return false;
	}

	protected function doProcess() {
		\BsArticleHelper::getInstance( $this->wikiPage->getTitle() )->invalidate();
	}

}
