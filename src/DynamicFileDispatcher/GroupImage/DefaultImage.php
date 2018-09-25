<?php

namespace BlueSpice\DynamicFileDispatcher\GroupImage;

use \BlueSpice\DynamicFileDispatcher\Module;
use BlueSpice\DynamicFileDispatcher\AbstractStaticFile;

class DefaultImage extends AbstractStaticFile {

	/**
	 *
	 * @return string
	 */
	protected function getAbsolutePath() {
		return $this->dfd->getConfig()->get( 'DefaultGroupImage' );
	}

}
