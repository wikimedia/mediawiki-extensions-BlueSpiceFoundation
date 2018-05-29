<?php

namespace BlueSpice\Hook\MediaWikiServices;

use BlueSpice\Hook\MediaWikiServices;

class ResetServices extends MediaWikiServices {

	protected function doProcess() {
		\BlueSpice\Services::resetInstance( $this->services );

		return true;
	}

}
