<?php

namespace BlueSpice\DynamicFileDispatcher\UserProfileImage;

use BlueSpice\DynamicFileDispatcher\AbstractStaticFile;

class DefaultImage extends AbstractStaticFile {

	/**
	 *
	 * @return string
	 */
	protected function getAbsolutePath() {
		return $this->dfd->getConfig()->get( 'DefaultUserImage' );
	}

}
