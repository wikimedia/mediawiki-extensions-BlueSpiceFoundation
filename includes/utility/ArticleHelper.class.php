<?php
/**
 * This class provides functions for common tasks while working with MediaWiki Article/Title objects.
 * @package BlueSpice_AdapterMW
 * @subpackage Utility
 */
class BsArticleHelper {
	protected $oTitle = null;
	protected $aPageProps = array();
	protected $bIsLoaded = false;
	
	protected static $aInstances = array();
	
	/**
	 * Protected constructor. Instances can be obtained through the factory 
	 * method of this class
	 * @param Title $oTitle
	 */
	protected function __construct( $oTitle ) {
		$this->oTitle = $oTitle;
	}
	
	/**
	 * N-glton implementation
	 * @param Title $oTitle
	 * @return BsArticleHelper
	 */
	public static function getInstance( $oTitle ) {
		$iArticleId = $oTitle->getArticleID();
		if( !isset( self::$aInstances[$iArticleId] ) ) {
			self::$aInstances[$iArticleId] = new BsArticleHelper( $oTitle );
		}

		return self::$aInstances[$iArticleId];
	}

	/**
	 * Fetches the number of edits in the discussion page of the given Title.
	 * @return int The number of edits of the talk page
	 */
	public function getDiscussionAmount() {
		$oTalkPage   = $this->oTitle->getTalkPage();
		$iTalkPageId = $oTalkPage->getArticleID();

		$dbr = wfGetDB( DB_SLAVE );
		// a new revision (rev_id) is also created on page move. So use rev_text_id
		$res = $dbr->select(
			'revision',
			'DISTINCT rev_text_id',
			array( 'rev_page' => $iTalkPageId ),
			__METHOD__
		);
		$iCount = $dbr->numRows( $res );
		return $iCount;
	}
	
	/**
	 * 
	 * @param string $sPropName
	 * @param bool $bDoLoad
	 * @return string|null
	 */
	public function getPageProp( $sPropName, $bDoLoad = false ) {
		if( $this->bIsLoaded == false || $bDoLoad == true ) {
			$this->loadPageProps();
		}

		if( !isset($this->aPageProps[$sPropName]) ) {
			return null;
		}

		return $this->aPageProps[$sPropName];
	}
	
	/**
	 * 
	 * @param string $sPropName
	 * @param bool $bDoLoad
	 * @return sdtClass|true|false|null See PHP documentation for json_decode()
	 */
	public function getJSONPageProp( $sPropName, $bDoLoad = false ) {
		return json_decode( 
			$this->getPageProp( $sPropName, $bDoLoad )
		);
	}


	public function getPageProps( $bDoLoad = false ) {
		if( $bDoLoad ) {
			$this->loadPageProps();
		}
		return $this->aPageProps;
	}

	/**
	 * Fetches the number of edits in the discussion page of the given Title.
	 * @param Title $oTitle The MediaWiki Title object to query.
	 * @return int The number of edits.
	 * @deprecated since version 1.20
	 */
	public static function getDiscussionAmountForTitle( $oTitle ) {
		return self::getInstance($oTitle)->getDiscussionAmount();
	}

	/**
	 * Fetches the number of edits in the discussion page of the given Article.
	 * @param Article $oArticle The MediaWiki Article object to query.
	 * @return int The number of edits.
	 * @deprecated since version 1.20
	 */
	public static function getDiscussionAmountForArticle( $oArticle ) {
		return self::getDiscussionAmountForTitle( $oArticle->getTitle() );
	}

	/**
	 * Fetches the page_props table
	 */
	public function loadPageProps() {
		$iArticleId = $this->oTitle->getArticleID();
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
			'page_props',
			array('pp_propname', 'pp_value'),
			array('pp_page' => $iArticleId ),
			__METHOD__
		);

		foreach( $res as $row ) {
			$this->aPageProps[$row->pp_propname] = $row->pp_value;
		}

		$this->bIsLoaded = true;;
	}

	/**
	 * Returns redirect target title or null if there is no redirect
	 * @return Title - returns redirect target title or null
	 */
	public function getTitleFromRedirectRecurse() {
		$oWikiPage = WikiPage::newFromID( $this->oTitle->getArticleID() );
		return $oWikiPage->getRedirectTarget();
	}

}