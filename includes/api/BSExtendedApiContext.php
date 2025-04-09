<?php

use MediaWiki\Context\RequestContext;
use MediaWiki\Json\FormatJson;
use MediaWiki\Request\WebRequest;
use MediaWiki\Title\Title;

class BSExtendedApiContext {

	/** @var Title */
	private $title;

	/** @var array */
	private $rawContextData = [];

	/**
	 * @param WebRequest|null $request
	 * @return BSExtendedApiContext
	 */
	public static function newFromRequest( $request = null ) {
		if ( !$request ) {
			$request = RequestContext::getMain()->getRequest();
		}

		/*
		 * Sync with JavaScript implementation of '_getContext' in
		 * 'bluespice.api.js'
		 * This is to meet standard MediaWiki behavior
		 */
		$defaultParams = [
			'wgAction' => 'view',
			'wgArticleId' => -1,
			'wgCanonicalNamespace' => false,
			'wgCanonicalSpecialPageName' => false,
			'wgRevisionId' => null,
			'wgNamespaceNumber' => 0,
			'wgPageName' => 'API',
			'wgRedirectedFrom' => null,
			'wgRelevantPageName' => 'API',
			'wgTitle' => 'API'
		];

		$requestParams = FormatJson::decode(
			$request->getVal( 'context', '{}' ),
			true
		) + $defaultParams;

		$title = Title::newFromID( $requestParams['wgArticleId'] );
		// e.g. on a SpecialPage
		if ( !$title ) {
			$title = Title::makeTitle(
				$requestParams['wgNamespaceNumber'],
				$requestParams['wgTitle']
			);
		}

		// TODO: Fallback if any of those is empty!
		$params = [
			'title' => $title,
			'rawdata' => $requestParams
		];

		return new self( $params );
	}

	private function __construct( $params ) {
		$this->title = $params['title'];
		$this->rawContextData = $params['rawdata'];
	}

	/**
	 * The context Title
	 * @return Title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * The raw data provided by the client
	 * @return array
	 */
	public function getRawContextData() {
		return $this->rawContextData;
	}

}
