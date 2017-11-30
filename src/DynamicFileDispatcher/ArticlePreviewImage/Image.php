<?php

namespace BlueSpice\DynamicFileDispatcher\ArticlePreviewImage;
use \BlueSpice\DynamicFileDispatcher\Module;

class Image extends \BlueSpice\DynamicFileDispatcher\File {

	/**
	 *
	 * @var \Title
	 */
	protected $title = null;

	/**
	 *
	 * @param Module $dfd
	 * @param \Title $title
	 */
	public function __construct( Module $dfd, \Title $title ) {
		parent::__construct( $dfd );
		$this->title = $title;
	}

	protected function getSourcePath() {
		return $GLOBALS['wgExtensionDirectory']
			."/BlueSpiceFoundation/resources/assets/article-preview-images/dummy.png";
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

		$path = \BsFileSystemHelper::normalizePath(
			$this->getSourcePath()
		);

		readfile( $path );
	}

	public function getMimeType() {
		return 'image/png';
	}
}