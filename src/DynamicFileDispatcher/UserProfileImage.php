<?php

namespace BlueSpice\DynamicFileDispatcher;

use BlueSpice\DynamicFileDispatcher\UserProfileImage\AnonImage;
use BlueSpice\DynamicFileDispatcher\UserProfileImage\DefaultImage;

class UserProfileImage extends Module {
	const MODULE_NAME = 'userprofileimage';
	const USERNAME = 'username';
	const WIDTH = 'width';
	const HEIGHT = 'height';

	/**
	 *
	 * @var \User
	 */
	protected $user = null;

	public function getParamDefinition() {
		return array_merge( parent::getParamDefinition(), [
			static::USERNAME => [
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
		if( empty( $this->params[static::USERNAME] ) ) {
			throw new \MWException(
				"Empty username parameter"
			);
		}
	}

	/**
	 * @return File
	 */
	public function getFile() {
		$this->user = \User::newFromName( $this->params[static::USERNAME] );
		if( !$this->user || $this->user->isAnon() ) {
			return new AnonImage( $this );
		}
		return new DefaultImage( $this );
	}
}
