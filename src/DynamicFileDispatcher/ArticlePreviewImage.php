<?php

namespace BlueSpice\DynamicFileDispatcher;

class ArticlePreviewImage extends Module {
	const TITLETEXT = 'titletext';
	const WIDTH = 'width';
	const HEIGHT = 'height';

	public function getParamDefinition() {
		return array_merge( parent::getParamDefinition(), [
			static::TITLETEXT => [
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
		if( !\Title::newFromText( $this->params[static::TITLETEXT] ) ) {
			throw new \MWException(
				"Invalid titletext: {$this->params[static::TITLETEXT]}"
			);
		}
	}

	/**
	 * @return File
	 */
	public function getFile() {
		return new \BlueSpice\DynamicFileDispatcher\ArticlePreviewImage\Image(
			$this,
			\Title::newFromText( $this->params[static::TITLETEXT] )
		);
	}
}