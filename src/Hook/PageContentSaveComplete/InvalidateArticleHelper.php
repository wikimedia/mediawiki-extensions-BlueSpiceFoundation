<?php

namespace BlueSpice\Hook\PageContentSaveComplete;
class InvalidateArticleHelper extends \BlueSpice\Hook\PageContentSaveComplete {

	protected function skipProcessing() {
		if( !$this->wikipage->getTitle()->exists() ) {
			return true;
		}

		return false;
	}

	protected function doProcess() {
		\BsArticleHelper::getInstance( $this->wikipage->getTitle() )->invalidate();
	}

}
