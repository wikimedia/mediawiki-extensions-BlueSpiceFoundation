<?php

namespace BlueSpice\Hook\TitleMoveComplete;

use BlueSpice\TargetCache\Title\Target;

class InvalidateTargetCacheTitle extends \BlueSpice\Hook\TitleMoveComplete {

	protected function doProcess() {
		$this->getServices()->getBSTargetCacheTitle()->invalidateAll(
			new Target( $this->title )
		);
		$this->getServices()->getBSTargetCacheTitle()->invalidateAll(
			new Target( $this->newTitle )
		);
		return true;
	}

}
