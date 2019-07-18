<?php

namespace BlueSpice\DynamicFileDispatcher;

use BlueSpice\Services;

class ArticlePreviewImage extends Module {
	const TITLETEXT = 'titletext';
	const REVISION = 'revid';
	const WIDTH = 'width';
	const HEIGHT = 'height';

	public function getParamDefinition() {
		return array_merge( parent::getParamDefinition(), [
			static::TITLETEXT => [
				Params::PARAM_TYPE => Params::TYPE_STRING,
				Params::PARAM_DEFAULT => '',
			],
			static::REVISION => [
				Params::PARAM_TYPE => Params::TYPE_INT,
				// TODO: config
				Params::PARAM_DEFAULT => 0,
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
		if ( !\Title::newFromText( $this->params[static::TITLETEXT] ) ) {
			throw new \MWException(
				"Invalid titletext: {$this->params[static::TITLETEXT]}"
			);
		}
		if ( !empty( $this->params[static::REVISION] ) ) {
			$store = Services::getInstance()->getRevisionStore();
			$revision = $store->getRevisionById(
				$this->params[static::REVISION]
			);
			if ( !$revision ) {
				$this->params[static::REVISION] = 0;
			}
		} else {
			$this->params[static::REVISION] = 0;
		}
	}

	/**
	 * @return File
	 */
	public function getFile() {
		$revision = null;
		if ( $this->params[static::REVISION] > 0 ) {
			$store = Services::getInstance()->getRevisionStore();
			$revision = $store->getRevisionById(
				$this->params[static::REVISION]
			);
		}
		return new \BlueSpice\DynamicFileDispatcher\ArticlePreviewImage\Image(
			$this,
			\Title::newFromText( $this->params[static::TITLETEXT] ),
			$revision
		);
	}
}
