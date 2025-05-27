<?php

namespace BlueSpice\Api\Format;

use MediaWiki\Api\ApiFormatBase;

/**
 * API formatter which is used for situations, when something really custom should be outputted.
 */
class None extends ApiFormatBase {

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		// Nothing to do here
	}

	/**
	 * @inheritDoc
	 */
	public function initPrinter( $unused = false ) {
		// Nothing to do here, thus output should be empty
	}

	/**
	 * @inheritDoc
	 */
	public function execute() {
		// Nothing to do here, thus output should be empty
	}

	/**
	 * @inheritDoc
	 */
	public function closePrinter() {
		// Nothing to do here, thus output should be empty
	}

	/**
	 * @inheritDoc
	 */
	public function getMimeType() {
		// MIME type is not used, so nothing to do here
	}
}
