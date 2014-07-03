<?php

class BsCAIContext {

	/**
	 *
	 * @var Title
	 */
	protected $oTitle = null;

	/**
	 *
	 * @var Revision
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
	 * @return BsCAContext
	 */
	public static function newFromRequest(){
		$oRequest = RequestContext::getMain()->getRequest();

		//TODO: Lazy init?
		$aRequestParams = array(
			'wgAction'                   => $oRequest->getVal('wgAction'),
			'wgArticleId'                => $oRequest->getVal('wgArticleId'),
			'wgCanonicalNamespace'       => $oRequest->getVal('wgCanonicalNamespace'),
			'wgCanonicalSpecialPageName' => $oRequest->getVal('wgCanonicalSpecialPageName'),
			'wgCurRevisionId'    => $oRequest->getVal('wgCurRevisionId'),
			//'wgIsArticle' => $oRequest->getVal('wgisArticle'),
			'wgNamespaceNumber'  => $oRequest->getVal('wgNamespaceNumber'),
			'wgPageName'         => $oRequest->getVal('wgPageName'),
			'wgRedirectedFrom'   => $oRequest->getVal('wgRedirectedFrom'),
			'wgRelevantPageName' => $oRequest->getVal('wgRelevantPageName'),
			'wgTitle'            => $oRequest->getVal('wgTitle')
		);

		$aParams = array(
			'title'        => Title::newFromID($aRequestParams['wgArticleId']),
			'revision'     => Revision::newFromId($aRequestParams['wgCurRevisionId']),
			'specialpage'  => SpecialPageFactory::getPage($aRequestParams['wgCanonicalSpecialPageName']),
			'relevantpage' => Title::newFromText($aRequestParams['wgRelevantPageName']),
		);

		return new self( $aParams );
	}

	private function __construct( $aParams ) {
		$this->oTitle        = $aParams['title'];
		$this->oRevision     = $aParams['revision'];
		$this->oSpecialPage  = $aParams['specialpage'];
		$this->oRelevantPage = $aParams['relevantpage'];
	}

	/**
	 *
	 * @return Title
	 */
	public function getTitle() {
		return $this->oTitle;
	}

	/**
	 *
	 * @return boolean
	 */
	public function isSpecialPage() {
		return $this->oTitle->isSpecialPage();
	}

	/**
	 *
	 * @return Revision
	 */
	public function getRevision() {
		return $this->oRevision;
	}

	/**
	 *
	 * @return SpecialPage
	 */
	public function getSpecialPage(){
		return $this->oSpecialPage;
	}
}

class BsCAContext extends BsCAIContext {}