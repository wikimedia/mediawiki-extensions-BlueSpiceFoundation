<?php

namespace BlueSpice\Hook\TitleMoveComplete;

use BlueSpice\TargetCache\Title\Target;

class InvalidateTargetCacheTitle extends \BlueSpice\Hook\TitleMoveComplete {

	protected function doProcess() {
		$this->getServices()->getService( 'BSTargetCacheTitle' )->invalidateAll(
			new Target( $this->title )
		);
		$this->getServices()->getService( 'BSTargetCacheTitle' )->invalidateAll(
			new Target( $this->newTitle )
		);
		return true;
	}

}
