<?php

use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;

/**
 * This class provides functions for common tasks while working with MediaWiki
 * Article/Title objects.
 * @package BlueSpice_AdapterMW
 * @subpackage Utility
 */
class BsArticleHelper {

	protected $oTitle = null;
	protected $bIsLoaded = false;

	protected static $aInstances = [];

	/** @var MediaWikiServices */
	protected $services;

	/**
	 * Protected constructor. Instances can be obtained through the factory
	 * method of this class
	 * @param Title $oTitle
	 */
	protected function __construct( $oTitle ) {
		$this->oTitle = $oTitle;
		$this->services = MediaWikiServices::getInstance();
	}

	/**
	 * N-glton implementation
	 * @param Title $oTitle
	 * @return BsArticleHelper
	 */
	public static function getInstance( $oTitle ) {
		$iArticleId = $oTitle->getArticleID();
		if ( !isset( self::$aInstances[$iArticleId] ) ) {
			self::$aInstances[$iArticleId] = new BsArticleHelper( $oTitle );
		}

		return self::$aInstances[$iArticleId];
	}

	/**
	 * DEPRECADED
	 * Fetches the number of edits in the discussion page of the given Title.
	 * @deprecated since version 3.1 - Not in use anymore
	 * @return int The number of edits of the talk page
	 */
	public function getDiscussionAmount() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$oTalkPage = $this->oTitle->getTalkPageIfDefined();

		if ( !$oTalkPage ) {
			return 0;
		}

		$iTalkPageId = $oTalkPage->getArticleID();

		$cacheHelper = $this->services->getService( 'BSUtilityFactory' )
			->getCacheHelper();
		$sKey = $cacheHelper->getCacheKey(
			'BlueSpice',
			'ArticleHelper',
			'getDiscussionAmount',
			$iTalkPageId
		);
		$aData = $cacheHelper->get( $sKey );

		if ( $aData !== false ) {
			wfDebugLog( 'bluespice', __CLASS__ . ': Fetching discussion amounts from cache' );
			$iCount = $aData['iCount'];
		} else {
			wfDebugLog( 'bluespice', __CLASS__ . ': Fetching discussion amounts from DB' );
			$dbr = $this->services->getDBLoadBalancer()->getConnection( DB_REPLICA );
			// a new revision (rev_id) is also created on page move. So use rev_text_id
			$res = $dbr->select(
				'revision',
				'DISTINCT rev_text_id',
				[ 'rev_page' => $iTalkPageId ],
				__METHOD__
			);
			$iCount = $res->numRows();

			$cacheHelper->set( $sKey, [ 'iCount' => $iCount ] );
		}

		return $iCount;
	}

	/**
	 * DEPRECATED
	 * @deprecated since version 3.1 - Use \BlueSpice\Services::getInstance()
	 * ->getService( 'BSUtilityFactory' )->getPagePropHelper( Title )->getPageProp instead
	 * @param string $sPropName
	 * @param bool $bDoLoad
	 * @return string|null
	 */
	public function getPageProp( $sPropName, $bDoLoad = false ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$helper = $this->services->getService( 'BSUtilityFactory' )
			->getPagePropHelper( $this->oTitle );
		return $helper->getPageProp( $sPropName );
	}

	/**
	 * DEPRECATED
	 * @deprecated since version 3.1 - Use FormatJson::decode in your code
	 * @param string $sPropName
	 * @param bool $bDoLoad
	 * @return sdtClass|bool|null See PHP documentation for json_decode()
	 */
	public function getJSONPageProp( $sPropName, $bDoLoad = false ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return json_decode(
			$this->getPageProp( $sPropName, $bDoLoad )
		);
	}

	/**
	 * DEPRECATED
	 * @deprecated since version 3.1 - Use \BlueSpice\Services::getInstance()
	 * ->getService( 'BSUtilityFactory' )->getPagePropHelper( Title )->getPageProps instead
	 * @param type $bDoLoad
	 * @return array
	 */
	public function getPageProps( $bDoLoad = false ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$helper = $this->services->getService( 'BSUtilityFactory' )
			->getPagePropHelper( $this->oTitle );
		return $helper->getPageProps();
	}

	/**
	 * Returns redirect target title or null if there is no redirect
	 * @return Title|null - returns redirect target title or null
	 */
	public function getTitleFromRedirectRecurse() {
		$oWikiPage = $this->services->getWikiPageFactory()
			->newFromID( $this->oTitle->getArticleID() );
		if ( !$oWikiPage ) {
			return null;
		}
		$redirTarget = $this->services->getRedirectLookup()->getRedirectTarget( $oWikiPage );

		return Title::castFromLinkTarget( $redirTarget );
	}

	public function invalidate() {
		$this->bIsLoaded = false;

		if ( !$this->oTitle->exists() || !$this->oTitle->getTalkPageIfDefined() ) {
			return true;
		}

		// $this->services fails CI
		$services = MediaWikiServices::getInstance();
		$cacheHelper = $services->getService( 'BSUtilityFactory' )->getCacheHelper();
		$talkPageTarget = $services->getNamespaceInfo()->getTalkPage( $this->oTitle );
		$talkPage = Title::newFromLinkTarget( $talkPageTarget );
		$sKey = $cacheHelper->getCacheKey(
			'BlueSpice',
			'ArticleHelper',
			'getDiscussionAmount',
			$talkPage->getArticleID()
		);

		$cacheHelper->invalidate( $sKey );
	}

	/**
	 * Retuns subpages of a given title alphabetical sorted by fullArticleText
	 *
	 * @param Title $oTitle
	 * @param int $iLimit
	 * @return array
	 */
	public static function getSubpagesSortedForTitle( Title $oTitle, $iLimit = -1 ) {
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
		foreach ( $aSubpages as $oSubpage ) {
			$aTitleArray[] = $oSubpage;
		}
		usort( $aTitleArray, static function ( $a, $b ) {
			return strcmp(
				strtolower( $a->getFullText() ),
				strtolower( $b->getFullText() )
			);
		} );

		return $aTitleArray;
	}

}
