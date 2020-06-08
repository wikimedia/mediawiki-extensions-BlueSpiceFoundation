<?php

use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\RevisionRecord;

class BSExtendedApiContext {

	/**
	 *
	 * @var Title
	 */
	protected $oTitle = null;

	/**
	 *
	 * @var RevisionRecord
	 */
	protected $oRevision = null;

	/**
	 *
	 * @var SpecialPage
	 */
	protected $oSpecialPage = null;

	/**
	 *
	 * @var Title
	 */
	protected $oRelevantPage = null;

	/**
	 *
	 * @var array
	 */
	protected $aRawContextData = [];

	/**
	 * @param WebRequest|null $oRequest
	 * @return BSExtendedApiContext
	 */
	public static function newFromRequest( $oRequest = null ) {
		if ( $oRequest === null ) {
			$oRequest = RequestContext::getMain()->getRequest();
		}

		/*
		 * Sync with JavaScript implementation of '_getContext' in
		 * 'bluespice.api.js'
		 * This is to meet standard MediaWiki behavior
		 */
		$aDefaultParams = [
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

		$aRequestParams = FormatJson::decode(
			$oRequest->getVal( 'context', '{}' ),
			true
		) + $aDefaultParams;

		$oTitle = Title::newFromID( $aRequestParams['wgArticleId'] );
		// e.g. on a SpecialPage
		if ( $oTitle instanceof Title === false ) {
			$oTitle = Title::makeTitle(
				$aRequestParams['wgNamespaceNumber'],
				$aRequestParams['wgTitle']
			);
		}

		$revisionLookup = MediaWikiServices::getInstance()->getRevisionLookup();

		// TODO: Fallback if any of those is empty!
		$aParams = [
			'title'        => $oTitle,
			'revision'     => $revisionLookup->getRevisionById( $aRequestParams['wgRevisionId'] ),
			'specialpage'  => \MediaWiki\MediaWikiServices::getInstance()
				->getSpecialPageFactory()
				->getPage( $aRequestParams['wgCanonicalSpecialPageName'] ),
			'relevantpage' => Title::newFromText( $aRequestParams['wgRelevantPageName'] ),
			'rawdata' => $aRequestParams
		];

		return new self( $aParams );
	}

	private function __construct( $aParams ) {
		$this->oTitle        = $aParams['title'];
		$this->oRevision     = $aParams['revision'];
		$this->oSpecialPage  = $aParams['specialpage'];
		$this->oRelevantPage = $aParams['relevantpage'];
		$this->aRawContextData = $aParams['rawdata'];
	}

	/**
	 * The context Title
	 * @return Title
	 */
	public function getTitle() {
		return $this->oTitle;
	}

	/**
	 * Whether the context Title is a SpecialPage
	 * @return bool
	 */
	public function isSpecialPage() {
		return $this->oTitle->isSpecialPage();
	}

	/**
	 * If call comes from a WikiPage, this returns a Revision object, otherwise
	 * NULL.
	 * Attention: This reflects the RevisionId that the client sees, _not_ the
	 * one that is the _latest_ to the context Title. It may be a old revision
	 * though.
	 * @return RevisionRecord
	 */
	public function getRevision() {
		return $this->oRevision;
	}

	/**
	 * If call comes from SpecialPage, this returns a SpecialPage object,
	 * otherwise NULL
	 * @return SpecialPage
	 */
	public function getSpecialPage() {
		return $this->oSpecialPage;
	}

	/**
	 * The raw data provided by the client
	 * @return array
	 */
	public function getRawContextData() {
		return $this->aRawContextData;
	}

}
