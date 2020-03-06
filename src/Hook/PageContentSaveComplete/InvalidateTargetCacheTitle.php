<?php

namespace BlueSpice\Hook\PageContentSaveComplete;

use BlueSpice\TargetCache\Title\Target;

class InvalidateTargetCacheTitle extends \BlueSpice\Hook\PageContentSaveComplete {

	protected function skipProcessing() {
		if ( !$this->wikipage->getTitle()->exists() ) {
			return true;
		}

		return false;
	}

	protected function doProcess() {
		$this->getServices()->getService( 'BSTargetCacheTitle' )->invalidateAll(
			new Target( $this->wikipage->getTitle() )
		);
		return true;
	}

}
