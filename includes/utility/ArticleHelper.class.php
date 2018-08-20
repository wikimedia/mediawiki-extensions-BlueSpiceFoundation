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

		$sKey = BsCacheHelper::getCacheKey( 'BlueSpice', 'ArticleHelper', 'getDiscussionAmount', $iTalkPageId );
		$aData = BsCacheHelper::get( $sKey );

		if( $aData !== false ) {
			wfDebugLog( 'BsMemcached', __CLASS__.': Fetching discussion amounts from cache' );
			$iCount = $aData['iCount'];
		} else {
			wfDebugLog( 'BsMemcached', __CLASS__.': Fetching discussion amounts from DB' );
			$dbr = wfGetDB( DB_REPLICA );
			// a new revision (rev_id) is also created on page move. So use rev_text_id
			$res = $dbr->select(
				'revision',
				'DISTINCT rev_text_id',
				array( 'rev_page' => $iTalkPageId ),
				__METHOD__
			);
			$iCount = $dbr->numRows( $res );

			BsCacheHelper::set( $sKey, array( 'iCount' =>  $iCount ) );
		}

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
	 * Fetches the page_props table
	 */
	public function loadPageProps() {
		$this->bIsLoaded = true;
		$this->aPageProps = array();
		if( !$this->oTitle->exists() ) {
			return;
		}

		$dbr = wfGetDB( DB_REPLICA );
		$res = $dbr->select(
			'page_props',
			array('pp_propname', 'pp_value'),
			array( 'pp_page' => $this->oTitle->getArticleID() ),
			__METHOD__
		);

		foreach( $res as $row ) {
			$this->aPageProps[$row->pp_propname] = $row->pp_value;
		}
	}

	/**
	 * Returns redirect target title or null if there is no redirect
	 * @return Title - returns redirect target title or null
	 */
	public function getTitleFromRedirectRecurse() {
		$oWikiPage = WikiPage::newFromID( $this->oTitle->getArticleID() );
		if( !$oWikiPage ) {
			return null;
		}
		return $oWikiPage->getRedirectTarget();
	}

	public function invalidate() {
		$this->bIsLoaded = false;
		$this->aPageProps = array();

		if( !$this->oTitle->exists() ) {
			return true;
		}

		$sKey = BsCacheHelper::getCacheKey(
			'BlueSpice',
			'ArticleHelper',
			'getDiscussionAmount',
			$this->oTitle->getTalkPage()->getArticleID()
		);

		BsCacheHelper::invalidateCache( $sKey );
	}

	/**
	 * Retuns subpages of a given title alphabetical sorted by fullArticleText
	 *
	 * @param \Title $oTitle
	 * @param int $iLimit
	 * @return array
	 */
	public static function getSubpagesSortedForTitle( \Title $oTitle, $iLimit = -1 ) {
		return self::getInstance( $oTitle )->getSubpagesSorted( $iLimit );
	}

	/**
	 * Retuns subpages alphabetical sorted by fullArticleText
	 * @param int $iLimit
	 * @return array
	 */
	public function getSubpagesSorted( $iLimit = -1 ) {
		$aSubpages = $this->oTitle->getSubpages( $iLimit );
		$aTitleArray = [];
		foreach( $aSubpages as $oSubpage ) {
			$aTitleArray[] = $oSubpage;
		}
		usort( $aTitleArray, function( $a, $b ) {
			return strcmp(
				strtolower( $a->getFullText() ),
				strtolower( $b->getFullText() )
			);
		});

		return $aTitleArray;
	}

}
