<?php

namespace BlueSpice\Hook\SetupAfterCache;

class AddConfigFiles extends \BlueSpice\Hook\SetupAfterCache {

	protected function doProcess() {
		foreach ( $this->getConfig()->get( 'ConfigFiles' ) as $sConfigFileKey => $sConfigFilePath ) {
			if ( file_exists( $sConfigFilePath ) ) {
				include $sConfigFilePath;
			}
		}

		return true;
	}

}
