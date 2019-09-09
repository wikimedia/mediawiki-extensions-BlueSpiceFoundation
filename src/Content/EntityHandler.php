<?php
namespace BlueSpice\Content;

abstract class EntityHandler extends \JsonContentHandler {

	/**
	 *
	 * @param string $modelId
	 */
	public function __construct( $modelId = '' ) {
		parent::__construct( $modelId );
	}

	/**
	 * @return string
	 */
	protected function getContentClass() {
		return "\\BlueSpice\\Content\\Entity";
	}
}
