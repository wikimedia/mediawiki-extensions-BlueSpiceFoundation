<?php

namespace BlueSpice\DynamicFileDispatcher;

use BlueSpice\DynamicFileDispatcher\UserProfileImage\AnonImage;
use BlueSpice\DynamicFileDispatcher\UserProfileImage\DefaultImage;
use MediaWiki\MediaWikiServices;

class UserProfileImage extends Module {
	public const MODULE_NAME = 'userprofileimage';
	public const USERNAME = 'username';
	public const WIDTH = 'width';
	public const HEIGHT = 'height';

	/**
	 *
	 * @var \User
	 */
	protected $user = null;

	/**
	 *
	 * @return array
	 */
	public function getParamDefinition() {
		return array_merge( parent::getParamDefinition(), [
			static::USERNAME => [
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
		if ( empty( $this->params[static::USERNAME] ) ) {
			throw new \MWException(
				"Empty username parameter"
			);
		}
	}

	/**
	 * @return File
	 */
	public function getFile() {
		$this->user = MediaWikiServices::getInstance()->getUserFactory()
			->newFromName( $this->params[static::USERNAME] );
		if ( !$this->user || !$this->user->isRegistered() ) {
			return new AnonImage( $this );
		}
		return new DefaultImage( $this );
	}
}
