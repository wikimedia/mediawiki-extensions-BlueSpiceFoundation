<?php

abstract class BSEntityContentHandler extends JsonContentHandler {

	public function __construct( $modelId = '' ) {
		parent::__construct( $modelId );
	}

	/**
	 * @return string
	 */
	protected function getContentClass() {
		return 'BSEntityContent';
	}
}