<?php

namespace BlueSpice\DynamicFileDispatcher\UserProfileImage;
use \BlueSpice\DynamicFileDispatcher\Module;

class Image extends \BlueSpice\DynamicFileDispatcher\File {
	/**
	 *
	 * @var string
	 */
	protected $src = '';

	/**
	 *
	 * @var \User
	 */
	protected $user = null;

	/**
	 *
	 * @param Module $dfd
	 * @param sring $src
	 * @param \User $user
	 */
	public function __construct( Module $dfd, $src, $user ) {
		parent::__construct( $dfd );
		$this->src = $src;
		$this->user = $user;
	}

	/**
	 * Sets the headers for given \WebResponse
	 * @param \WebResponse $response
	 * @return void
	 */
	public function setHeaders( \WebResponse $response ) {
		$response->header(
			'Content-type: '.$this->getMimeType(),
			true
		);
		//This is temporay code until the UserMiniProfile gets a rewrite
		$path = $GLOBALS['IP'];
		$scriptPath = $this->dfd->getConfig()->get( 'ScriptPath' );
		if( $scriptPath && $scriptPath != "" ) {
			$countDirs = substr_count( $scriptPath, '/' );
			$i = 0;
			while( $i < $countDirs ) {
				$path = dirname( $path );
				$i++;
			}
		}
		$path = str_replace(
			'/nsfr_img_auth.php/',
			'/images/',
			\BsFileSystemHelper::normalizePath( $path.$this->src )
		);

		readfile( $path );
	}

	public function getMimeType() {
		return 'image/png';
	}
}