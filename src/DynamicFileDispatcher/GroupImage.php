<?php

namespace BlueSpice\DynamicFileDispatcher;

use BlueSpice\DynamicFileDispatcher\GroupImage\DefaultImage;

class GroupImage extends Module {
	public const MODULE_NAME = 'groupimage';
	public const GROUP = 'group';
	public const WIDTH = 'width';
	public const HEIGHT = 'height';

	/**
	 *
	 * @return array
	 */
	public function getParamDefinition() {
		return array_merge( parent::getParamDefinition(), [
			static::GROUP => [
				Params::PARAM_TYPE => Params::TYPE_STRING,
				Params::PARAM_DEFAULT => '',
			],
			static::WIDTH => [
				Params::PARAM_TYPE => Params::TYPE_INT,
				// TODO: config
				Params::PARAM_DEFAULT => 40,
			],
			static::HEIGHT => [
				Params::PARAM_TYPE => Params::TYPE_INT,
				// TODO: config
				Params::PARAM_DEFAULT => 40,
			],
		] );
	}

	/**
	 *
	 * @param Params $params
	 */
	protected function extractParams( $params ) {
		parent::extractParams( $params );
		if ( empty( $this->params[static::GROUP] ) ) {
			throw new \MWException(
				"Empty 'group' parameter"
			);
		}
	}

	/**
	 * @return File
	 */
	public function getFile() {
		return new DefaultImage( $this );
	}
}
