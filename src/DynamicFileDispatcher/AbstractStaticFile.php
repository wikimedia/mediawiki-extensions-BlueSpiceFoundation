<?php

namespace BlueSpice\DynamicFileDispatcher;

use BlueSpice\Services;

abstract class AbstractStaticFile extends File {

	/**
	 * @return string
	 */
	public function getMimeType() {
		$mimeAnalyzer = Services::getInstance()->getMimeAnalyzer();
		return $mimeAnalyzer->guessMimeType( $this->getAbsolutePath() );
	}

	/**
	 * Sets the headers for given \WebResponse
	 * @param \WebResponse $response
	 * @return void
	 */
	public function setHeaders( \WebResponse $response ) {
		$response->header( 'Content-type: ' . $this->getMimeType(), true );
		readfile( $this->getAbsolutePath() );
	}

	/**
	 * @return string
	 */
	protected abstract function getAbsolutePath();
}
