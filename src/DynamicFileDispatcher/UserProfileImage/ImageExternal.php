<?php

namespace BlueSpice\DynamicFileDispatcher\UserProfileImage;

class ImageExternal extends Image {

	/**
	 * Sets the headers for given \WebResponse
	 * @param \WebResponse $response
	 * @return void
	 */
	public function setHeaders( \WebResponse $response ) {
		$this->dfd->getContext()->getRequest()->response()->header(
			"Location:$this->src",
			true
		);
	}

	public function getMimeType() {
		return '';
	}
}