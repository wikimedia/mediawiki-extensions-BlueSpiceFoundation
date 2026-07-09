<?php

namespace BlueSpice\Hook\PageSaveComplete;

use BlueSpice\Hook\PageSaveComplete;
use BlueSpice\TargetCache\Title\Target;

class InvalidateTargetCacheTitle extends PageSaveComplete {

	/**
	 * @return bool
	 */
	protected function skipProcessing() {
		if ( !$this->wikiPage->getTitle()->exists() ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	protected function doProcess() {
		$this->getServices()->getService( 'BSTargetCacheTitle' )->invalidateAll(
			new Target( $this->wikiPage->getTitle() )
		);
		return true;
	}

}
