<?php

namespace BlueSpice\DynamicFileDispatcher;

class UserProfileImage extends Module {
	const USERNAME = 'username';
	const WIDTH = 'width';
	const HEIGHT = 'height';

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
		if( !\User::newFromName( $this->params[static::USERNAME] ) ) {
			throw new \MWException(
				"Invalid username: {$this->params[static::USERNAME]}"
			);
		}
	}

	protected function getImageSource() {
		//This is temporay code until the UserMiniProfile gets a rewrite
		$miniprofile = \BsCore::getInstance()->getUserMiniProfile(
			\User::newFromName( $this->params[static::USERNAME] ),
			[
				'width' => $this->params[static::WIDTH],
				'height' => $this->params[static::HEIGHT],
			]
		);
		$options = $miniprofile->getOptions();
		return $options['userimagesrc'];
	}

	protected function isExternalUrl( $sMaybeExternalUrl ) {
		return substr( $sMaybeExternalUrl, 0, 4 ) == "http";
	}

	/**
	 * @return File
	 */
	public function getFile() {
		$imgSrc = $this->getImageSource();
		if( $this->isExternalUrl( $imgSrc ) ) {
			return new \BlueSpice\DynamicFileDispatcher\UserProfileImage\ImageExternal(
				$this,
				$imgSrc,
				\User::newFromName( $this->params[static::USERNAME] )
			);
		}
		return new \BlueSpice\DynamicFileDispatcher\UserProfileImage\Image(
			$this,
			$imgSrc,
			\User::newFromName( $this->params[static::USERNAME] )
		);
	}
}