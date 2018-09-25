<?php

namespace BlueSpice\DynamicFileDispatcher;

use BlueSpice\DynamicFileDispatcher\GroupImage\DefaultImage;

class GroupImage extends Module {
	const MODULE_NAME = 'groupimage';
	const GROUP = 'group';
	const WIDTH = 'width';
	const HEIGHT = 'height';

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
				Params::PARAM_DEFAULT => 40, //TODO: config
			],
			static::HEIGHT => [
				Params::PARAM_TYPE => Params::TYPE_INT,
				Params::PARAM_DEFAULT => 40, //TODO: config
			],
		]);
	}

	/**
	 *
	 * @param Params $params
	 */
	protected function extractParams( $params ) {
		parent::extractParams( $params );
		if( empty( $this->params[static::GROUP] ) ) {
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
