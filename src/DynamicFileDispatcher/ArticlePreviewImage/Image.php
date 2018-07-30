<?php

namespace BlueSpice\DynamicFileDispatcher\ArticlePreviewImage;

use BlueSpice\DynamicFileDispatcher\Module;
use MediaWiki\Storage\RevisionRecord;
use BlueSpice\Services;

class Image extends \BlueSpice\DynamicFileDispatcher\File {

	/**
	 *
	 * @var \Title
	 */
	protected $title = null;

	/**
	 *
	 * @var RevisionRecord
	 */
	protected $revision = null;

	/**
	 *
	 * @param Module $dfd
	 * @param \Title $title
	 */
	public function __construct( Module $dfd, \Title $title, RevisionRecord $revision = null ) {
		parent::__construct( $dfd );
		$this->title = $title;
		$this->revision = $revision;
		if( !$this->revision ) {
			$store = Services::getInstance()->getRevisionStore();
			$this->revision = $store->getRevisionByTitle( $title );
		}

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

		readfile( $this->getSourcePath() );
	}

	public function getMimeType() {
		return 'image/png';
	}
}
