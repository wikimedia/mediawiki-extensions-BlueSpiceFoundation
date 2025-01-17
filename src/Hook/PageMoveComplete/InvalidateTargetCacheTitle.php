<?php

namespace BlueSpice\Hook\PageMoveComplete;

use BlueSpice\Hook\PageMoveComplete;
use BlueSpice\TargetCache\Title\Target;
use MediaWiki\Title\Title;

class InvalidateTargetCacheTitle extends PageMoveComplete {

	protected function doProcess() {
		$this->getServices()->getService( 'BSTargetCacheTitle' )->invalidateAll(
			new Target( Title::newFromLinkTarget( $this->old ) )
		);
		$this->getServices()->getService( 'BSTargetCacheTitle' )->invalidateAll(
			new Target( Title::newFromLinkTarget( $this->new ) )
		);
		return true;
	}

}
