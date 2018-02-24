<?php

namespace BlueSpice\DynamicFileDispatcher\UserProfileImage;

use \BlueSpice\DynamicFileDispatcher\Module;
use BlueSpice\DynamicFileDispatcher\AbstractStaticFile;

class AnonImage extends AbstractStaticFile {

	protected function getAbsolutePath() {
		return $this->dfd->getConfig()->get( 'DefaultAnonImage' );
	}
}
