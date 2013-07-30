<?php

//index.php?action=ajax&rs=BSCommonAJAXInterface::getTitleStoreData&rsargs[]={}
//index.php?action=ajax&rs=BSCommonAJAXInterface::getNamespaceStoreData&rsargs[]={}

class BsCommonAJAXInterface {
	public static function getTitleStoreData( $sOptions ) {
		//TODO: Reflect $options ans WebRequest::getVal('start|limit|...')
		$aOptions = FormatJson::decode($sOptions);
		
		$aConditions = array();
		
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'page', '*', $aConditions );
		$oResult = new stdClass();
		$oResult->titles = array();
		foreach( $res as $row ){
			$oTitle = Title::newFromRow($row);
			if( $oTitle->userCan( 'read') == false ) continue; //TODO: Maybe reflect in PAGING!

			$oClientTitle = new stdClass();
			$oClientTitle->articleId = $oTitle->getArticleID();
			$oClientTitle->text = $oTitle->getText();
			$oClientTitle->prefixedText = $oTitle->getPrefixedText();
			$oClientTitle->namespaceId = $oTitle->getNamespace();
			$oClientTitle->namespaceText = $oTitle->getNsText();
			$oClientTitle->isRedirect = $oTitle->isRedirect();
			$oClientTitle->isSubpage = $oTitle->isSubpage();
			
			$oResult->titles[] = $oClientTitle;
		}
		
		$aSpecialPages = SpecialPageFactory::getList();
		$aSP = array();
		foreach( $aSpecialPages as $sSpecialPageName => $sSpecialPageAlias ) {
			$oSpecialPage = SpecialPage::getPage( $sSpecialPageName );
			if( $oSpecialPage instanceof SpecialPage == false ){
				wfDebug( __METHOD__.': "'.$sSpecialPageName.'" is not a valid SpecialPage' );
				continue;
			}
			
			$oTitle = $oSpecialPage->getTitle();

			$oClientTitle = new stdClass();
			$oClientTitle->articleId = -1;
			$oClientTitle->text = $oSpecialPage->getDescription();;
			$oClientTitle->prefixedText = $oTitle->getPrefixedText();
			$oClientTitle->namespaceId = $oTitle->getNamespace();
			$oClientTitle->namespaceText = $oTitle->getNsText();
			$oClientTitle->isRedirect = false;
			$oClientTitle->isSubpage = false;
			
			$oResult->titles[] = $oClientTitle;
		}
		
		return FormatJson::encode($oResult);
	}
	
	public static function getNamespaceStoreData( $sOptions ) {
		//TODO: Reflect $options ans WebRequest::getVal('start|limit|...')
		$aOptions = FormatJson::decode($sOptions);

		$aNamespaces = BsNamespaceHelper::getNamespacesForSelectOptions();
		$oResult = new stdClass();
		$oResult->namespaces = array();
		foreach( $aNamespaces as $iNSid => $sNSName ){
			$oNamespace = new stdClass();
			$oNamespace->namespaceId = $iNSid;
			$oNamespace->namespaceName = $sNSName;
			$oNamespace->isNonincludable = MWNamespace::isNonincludable( $iNSid );
			$oNamespace->namespaceContentModel = MWNamespace::getNamespaceContentModel( $iNSid );

			$oResult->namespaces[] = $oNamespace;
		}
		
		return FormatJson::encode($oResult);
	}
}


class BsCommonAjaxInterfaceContext {
	
	protected $oTitle = null;
	protected $oRevision = null;
	protected $oSpecialPage = null;
	
	public static function newFromRequest(){
		$oRequest = RequestContext::getMain()->getRequest();
		
		$aRequestParams = array(
			'action'                   => $oRequest->getVal('action'),
			'articleId'                => $oRequest->getVal('articleId'),
			'canonicalNamespace'       => $oRequest->getVal('canonicalNamespace'),
			'canonicalSpecialPageName' => $oRequest->getVal('canonicalSpecialPageName'),
			'curRevisionId'    => $oRequest->getVal('curRevisionId'),
			//'isArticle' => $oRequest->getVal('isArticle'),
			'namespaceNumber'  => $oRequest->getVal('namespaceNumber'),
			'pageName'         => $oRequest->getVal('pageName'),
			'redirectedFrom'   => $oRequest->getVal('redirectedFrom'),
			'relevantPageName' => $oRequest->getVal('relevantPageName'),
			'title'            => $oRequest->getVal('title')
		);
		
		//TODO: implement...
		$oTitle        = Title::newFromID($aRequestParams['articleId']);
		$oRevision     = Revision::newFromId($aRequestParams['curRevisionId']);
		$oSpecialPage  = SpecialPageFactory::getPage($aRequestParams);
		$oRelevantPage = Title::newFromText($aRequestParams);
	}
	
	private function __construct() {
	}
	
	public function getTitle() {
		
	}
	
	public function isSpecialPage() {
		
	}
	
	
}