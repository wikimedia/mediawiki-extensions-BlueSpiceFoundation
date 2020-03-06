<?php

namespace BlueSpice\Hook\ArticleDeleteComplete;

use BlueSpice\TargetCache\Title\Target;

class InvalidateTargetCacheTitle extends \BlueSpice\Hook\ArticleDeleteComplete {

	protected function doProcess() {
		$this->getServices()->getService( 'BSTargetCacheTitle' )->invalidateAll(
			new Target( $this->wikipage->getTitle() )
		);
		return true;
	}

}
