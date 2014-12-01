<?php

class BsCommonAJAXInterface {

	/**
	 * Returns a List of Titles for the client side
	 * @global Language $wgLang
	 * @param string $sOptions JSON formatted options array
	 * @return BsCAResponse
	 */
	public static function getTitleStoreData( $sOptions = '{}' ) {
		global $wgContLang;
		$oResponse = BsCAResponse::newFromPermission( 'read' );
		if ( $oResponse->isSuccess() === false ) {
			return $oResponse;
		}

		$oContext = RequestContext::getMain();
		$oParams = BsExtJSStoreParams::newFromRequest();
		$aOptions = FormatJson::decode( $sOptions, true ) + array(
			'limit' => 100,
			'namespaces' => array(),
			'returnQuery' => false
		);

		$sQuery = strtolower( $oParams->getQuery() );
		$sQuery = str_replace( '_', ' ', $sQuery );

		//See JS BS.model.Title
		$aPayload = array();
		$aDataSet = array(
			'page_id' => 0,
			'page_namespace' => 0,
			'page_title' => '',
			'prefixedText' => '',
			'displayText' => '',
			'type' => 'wikipage'
		);

		//Step 1: Collect namespaces
		$aNamespaces = $wgContLang->getNamespaces();
		asort( $aNamespaces );
		foreach ( $aNamespaces as $iNsId => $sNamespaceText ) {
			if ( empty( $sNamespaceText ) ) {
				continue;
			}

			if ( !in_array( $iNsId, $aOptions['namespaces'] ) ) {
				continue;
			}

			$sNormNSText = strtolower( $sNamespaceText );
			$sNormNSText = str_replace( '_', ' ', $sNormNSText );

			if ( empty( $sQuery ) || strpos( $sNormNSText, $sQuery ) === 0) {

				//Only namespaces a user has the read permission for
				$oDummyTitle = Title::newFromText($sNamespaceText.':X');
				if( $oDummyTitle->userCan('read') === false ) {
					continue;
				}

				$aPayload[] = array(
					'type' => 'namespace',
					'displayText' => $sNamespaceText.':'
				) + $aDataSet;
			}
		}

		if ( empty( $sQuery ) ) {
			$oResponse->setPayload( $aPayload );
			return $oResponse;
		}

		//Step 2: Find pages
		$oQueryTitle = Title::newFromText( $oParams->getQuery() );

		if ( $oQueryTitle instanceof Title === false ) {
			$oResponse->setPayload( $aPayload );
			return $oResponse;
		}

		//This is an ugly workaround to archive a case insensitive lookup of
		//page titles. Even though the 'searchindex.si_title' column saves a
		//lower cased version of the title the standard MediaWiki APIs
		//(SearchEngine, *PrefixSearch, API modules 'search' and
		//'prefixsearch') do only case sensitive lookups. A good solution could
		//be to use the TitleKey extension by Brion Vibber
		//(https://www.mediawiki.org/wiki/Extension:TitleKey) to have a
		//consistent case insensitive search behavior.
		//The current approach has a major disadvantage: It does not find
		//anything when the query contains a hyphen! Also be careful
		//the PrefixSearch returns SpecialPages with their aliases.

		$dbr = wfGetDB( DB_SLAVE );

		// We want LIKE operator behind every term,
		// so multi term queries also bring results
		$sOp = $dbr->anyString();
		$aLike = array( '', $sOp );
		$sParams = explode( ' ', strtolower( $oQueryTitle->getText() ) );
		foreach ( $sParams as $sParam ) {
			$aLike[] = $sParam;
			$aLike[] = $sOp;
		}

		$res = $dbr->select(
			array( 'page', 'searchindex' ),
			array( 'page_id' ),
			array(
				'page_id = si_page',
				'si_title '. $dbr->buildLike(
					$aLike
				),
				'page_namespace' => $oQueryTitle->getNamespace()
			),
			__METHOD__,
			array(
				'LIMIT' => $aOptions['limit']
			)
		);

		$aTitles = array();
		foreach ( $res as $row ) {
			$oTitle = Title::newFromID( $row->page_id );
			if ( $oTitle->userCan( 'read' ) === false ) continue;

			$aTitles[] = $oTitle;
		}

		if ( $aOptions['returnQuery'] === true ) {
			//We prepend the query title to the list of titles
			array_unshift( $aTitles, $oQueryTitle );
		}

		foreach ( $aTitles as $oTitle ) {
			//If we return the query itself we have to filter out a potential
			//match found by the search
			if ( $aOptions['returnQuery'] === true ) {
				if ( $oQueryTitle !== $oTitle && $oQueryTitle->equals( $oTitle ) ) {
					continue;
				}
			}

			$sPrefixedText = $oTitle->getPrefixedText();
			$aPayload[] = array(
				'type' => 'wikipage',
				'page_id' => $oTitle->getArticleId(),
				'page_namespace' => $oTitle->getNamespace(),
				'page_title' => $oTitle->getText(),
				'prefixedText' => $sPrefixedText,
				'displayText' => $sPrefixedText
			) + $aDataSet;
		}

		//Step 3: Find Specialpages
		//Add specialpages that are not held in the database.
		//This needs some more thinking: At the moment we calculate both a
		//"prefixedText" that can be used for linking and URLs in general - and
		//a "displayText" that the user sees. This is because the average
		//SpecialPage name looks pretty unfamiliar to a user, because on
		//Special:Specialpages and in the SpecialPages themselfs a
		//"description" text is used. It is especially true for english
		//language.
		//In means of a "Title" the canonical names woulb be better, becaus you
		//cannot link or access a SpecialPage by its description
		$aSpecialPages = SpecialPageFactory::getList();
		$aSPDataSets = array();
		$aSortHelper = array();
		$aClassNames = array();
		foreach ( $aSpecialPages as $sSpecialPageName => $sClassName ) {

			//Prevent double listing
			if ( in_array( $sClassName, $aClassNames ) ) {
				continue;
			}

			$aClassNames[] = $sClassName;

			$oSpecialPage = SpecialPageFactory::getPage($sSpecialPageName);
			if ( !( $oSpecialPage instanceof SpecialPage ) ){
				wfDebug( __METHOD__.': "'.$sSpecialPageName.'" is not a valid SpecialPage' );
				continue;
			}

			//This seems awkward. There has to be a better way...
			$sMsgKey = strtolower( $sSpecialPageName );
			$sSPDisplayText = Title::makeTitle(
				NS_SPECIAL, wfMessage($sMsgKey)->inContentLanguage()->plain()
			)->getPrefixedText();

			if ( strpos( strtolower( $sSPDisplayText ), $sQuery ) !== 0 ) {
				continue;
			}

			if ( $oSpecialPage->isListed() == false ) {
				continue;
			}

			//Filter out SpecialPages that the current user may not execute
			if ( !$oSpecialPage->userCanExecute( $oContext->getUser() ) ) {
				continue;
			}

			$sSPText = $oSpecialPage->getPageTitle()->getPrefixedText();

			$aSPDataSets[] = array(
				'type' => 'specialpage',
				'prefixedText' => $sSPText,
				'displayText' => $sSPDisplayText
			) + $aDataSet;

			$aSortHelper[] = $sSPDisplayText;
		}

		//We want the result to be sorted by its display text!
		array_multisort( $aSortHelper, SORT_NATURAL, $aSPDataSets );

		$aPayload += $aSPDataSets;

		$oResponse->setPayload( $aPayload );
		return $oResponse;
	}

	//index.php?action=ajax&rs=BSCommonAJAXInterface::getNamespaceStoreData&rsargs[]={}
	public static function getNamespaceStoreData( $sOptions = '{}' ) {
		//TODO: Reflect $options ans WebRequest::getVal('start|limit|...')
		//$aOptions = FormatJson::decode( $sOptions, true );

		$aNamespaces = BsNamespaceHelper::getNamespacesForSelectOptions();
		$oResult = new stdClass();
		$oResult->namespaces = array();

		foreach ( $aNamespaces as $iNSid => $sNSName ){
			$oNamespace = new stdClass();
			$oNamespace->namespaceId = $iNSid;
			$oNamespace->namespaceName = $sNSName;
			$oNamespace->isNonincludable = MWNamespace::isNonincludable( $iNSid );
			$oNamespace->namespaceContentModel = MWNamespace::getNamespaceContentModel( $iNSid );

			$oResult->namespaces[] = $oNamespace;
		}

		return FormatJson::encode($oResult);
	}

	public static function getUserStoreData( $sOptions = '{}' ) {
		$aDefaultOptions = array(
			'group' => '', //TODO: make array? How to operate them? AND / OR
			'permission' => '', //TODO: see above
		);

		//$aOptions = FormatJson::decode($sOptions, true);
		//TODO: recursive merge with $aDefaultOptions

		$oResult = new stdClass();
		$oResult->users = array();

		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
			'user',
			'user_id'
		);

		foreach( $res as $row ) {
			$oUser = User::newFromId( $row->user_id );
			$oUserData = new stdClass();

			//DB fields
			//PW: user_id needs to be casted to int or ExtJs can not search the store by id property!
			$oUserData->user_id = (int) $oUser->getId();
			$oUserData->user_name = $oUser->getName();

			//Calculated fields
			$oUserData->display_name = BsCore::getUserDisplayName( $oUser );
			$oUserData->page_prefixed_text = $oUser->getUserPage()->getPrefixedText();

			$oResult->users[] = $oUserData;
		}

		return FormatJson::encode( $oResult );
	}

	public static function getAsyncCategoryTreeStoreData( $sOptions = '{}' ) {
		global $wgRequest;
		$aResult = array();
		$sNode = $wgRequest->getVal( 'node' );
		$dbr = wfGetDB( DB_SLAVE );

		if ( $sNode == 'src' ) {
			$aCategories = array();
			$aSubCategories = array();

			$resSubcats = $dbr->select(
				array( 'page', 'categorylinks' ),
				array( 'page_title AS cat_title' ),
				array( 'page_namespace' => NS_CATEGORY ),
				__METHOD__,
				array( 'ORDER BY page_title' ),
				array( 'categorylinks' => array( 'INNER JOIN', 'page_id = cl_from' ), )
			);

			foreach ( $resSubcats as $row ) {
				$aSubCategories[] = $row->cat_title;
			}

			$aTables = array( 'page' );
			$aFields = array( '*' );
			$aConditions = array( 'page_namespace' => NS_CATEGORY );
			$sMethod = __METHOD__;
			$aOptions = array( '');
			$aJoinConds = array();

			$aConditions[] = 'page_title NOT IN (\'' . implode( '\', \'', $aSubCategories ) . '\')';

			$resPageTable = $dbr->select(
				$aTables,
				$aFields,
				$aConditions,
				$sMethod,
				$aOptions,
				$aJoinConds
			);

			foreach ( $resPageTable as $row ) {
				$aCategories[] = $row->page_title;
			}

			$resCategoryTable = $dbr->select(
				array( 'category', 'categorylinks' ),
				'cat_title',
				array(
					'cat_title NOT IN (\'' . implode( '\', \'', $aSubCategories ) . '\')',
					'cat_title = cl_to'
				),
				__METHOD__,
				array( 'GROUP BY cat_title' )
			);

			foreach ( $resCategoryTable as $row ) {
				$aCategories[] = $row->cat_title;
			}

			$aCategories = array_unique( $aCategories );
			asort( $aCategories );

			foreach ( $aCategories as $sCategory ) {
				$oTmpCat = Category::newFromName( $sCategory );
				if ( $oTmpCat instanceof Category ){}
				$oCategory = new stdClass();
				$oCategory->text = str_replace( '_', ' ', $oTmpCat->getName() );
				$oCategory->leaf = ( $oTmpCat->getSubcatCount() > 0 ) ? false : true;
				$oCategory->id = 'src/'.str_replace('/', '+', $oCategory->text );
				$aResult[] = $oCategory;
			}
		} else {
			$aNodes = explode( '/', $sNode );
			$sCatTitle = str_replace( '+', '/', str_replace( ' ', '_', array_pop( $aNodes ) ) );

			$aTables = array( 'page', 'categorylinks' );
			$aFields = array( 'page_title' );
			$aConditions = array( 'cl_to' => $sCatTitle, 'page_namespace' => NS_CATEGORY );
			$sMethod = __METHOD__;
			$aOptions = array( '' );
			$aJoinConds = array( 'categorylinks' => array( 'INNER JOIN', 'page_id=cl_from') );

			$resSubCategories = $dbr->select(
				$aTables,
				$aFields,
				$aConditions,
				$sMethod,
				$aOptions,
				$aJoinConds
			);

			$aSubCategories = array();

			foreach ( $resSubCategories as $row ) {
				$aSubCategories[] = $row->page_title;
			}
			asort( $aSubCategories );

			foreach ( $aSubCategories as $sCategory ) {
				$oTmpCat = Category::newFromName( $sCategory );
				$oCategory = new stdClass();
				$oCategory->text = str_replace( '_', ' ', $oTmpCat->getName() );
				$oCategory->leaf = ( $oTmpCat->getSubcatCount() > 0 ) ? false : true;
				$oCategory->id = $sNode.'/'.str_replace('/', '+', $oCategory->text );
				$aResult[] = $oCategory;
			}
		}

		return FormatJson::encode( $aResult );
	}

	public static function getCategoryStoreData( $sOptions = '{}' ) {
		// $sOptions will be used... maybe
		$oResult = new stdClass();

		$aCategories = array();
		$dbr = wfGetDB( DB_SLAVE );
		// category table also tracks all deleted categories. So we need to double
		// check with categorylinks and page table. Use case for this is a category
		// name that had a spelling mistake.
		// From category table:
		// -- Track all existing categories.  Something is a category if 1) it has an en-
		// -- try somewhere in categorylinks, or 2) it once did.  Categories might not
		// -- have corresponding pages, so they need to be tracked separately.

		// (31.01.14) STM: Query had to be seperated into two quieres because it was to expensive

		$res = $dbr->select(
			array( 'category', 'categorylinks' ),
			array( 'cat_id', 'cat_title', 'cat_pages', 'cat_subcats', 'cat_files' ),
			array( 'cat_title = cl_to' ),
			__METHOD__,
			array( 'GROUP BY' => 'cat_title' )
		);

		foreach ( $res as $row ) {
			$oCategoryTitle = Title::newFromText( $row->cat_title, NS_CATEGORY );
			if ( !is_object( $oCategoryTitle ) ) {
				continue;
			}

			$oCategoryData = new stdClass();

			$oCategoryData->cat_id = (int)$row->cat_id;
			$oCategoryData->cat_title = $row->cat_title;
			$oCategoryData->text = $row->cat_title;
			$oCategoryData->cat_pages = (int)$row->cat_pages;
			$oCategoryData->cat_subcats = (int)$row->cat_subcats;
			$oCategoryData->cat_files = (int)$row->cat_files;

			$oCategoryData->prefixed_text = $oCategoryTitle->getPrefixedText();

			$aCategories[$row->cat_title] = $oCategoryData;
		}

		$res = $dbr->select(
			array( 'category', 'page' ),
			array( 'cat_id', 'cat_title', 'cat_pages', 'cat_subcats', 'cat_files' ),
			array( 'cat_title = page_title AND page_namespace = '.NS_CATEGORY )
		);

		foreach ( $res as $row ) {
			$oCategoryTitle = Title::newFromText( $row->cat_title, NS_CATEGORY );
			if ( !is_object( $oCategoryTitle ) ) {
				continue;
			}

			$oCategoryData = new stdClass();

			$oCategoryData->cat_id = (int)$row->cat_id;
			$oCategoryData->cat_title = $row->cat_title;
			$oCategoryData->text = $row->cat_title;
			$oCategoryData->cat_pages = (int)$row->cat_pages;
			$oCategoryData->cat_subcats = (int)$row->cat_subcats;
			$oCategoryData->cat_files = (int)$row->cat_files;

			$oCategoryData->prefixed_text = $oCategoryTitle->getPrefixedText();

			$aCategories[$row->cat_title] = $oCategoryData;
		}

		ksort( $aCategories );
		$aCategories = array_values( $aCategories );

		$oResult->categories = $aCategories;
		return FormatJson::encode( $oResult );
	}

	/**
	 * Calculate the real file path of an image to show an preview.
	 * @deprecated Use MW API with query prop "imageinfo" and iiprop "url"
	 * http://www.mediawiki.org/wiki/API:Properties#imageinfo_.2F_ii
	 * @param type $output The ajax output which have to be valid JSON.
	 */
	public static function getFileUrl( $file ) {
		$url = self::imageUrl( $file );
		//TODO: This is not good. We should use API and SecureFileStore should
		//alter API response via Hook
		if ( BsExtensionManager::isContextActive( 'MW::SecureFileStore::Active' ) ) {
			$url = SecureFileStore::secureStuff( $url, true );
		}
		return FormatJson::encode(
			array(
				'file' => $file,
				'url' => $url
			)
		);
	}

	/**
	 * Helper method for self::getFileRealLink
	 * @deprecated Use MW API with query prop "imageinfo" and iiprop "url"
	 * http://www.mediawiki.org/wiki/API:Properties#imageinfo_.2F_ii
	 * @param string $name
	 * @param boolean $fromSharedDirectory
	 * @return string The url of the file with name $name
	 */
	protected static function imageUrl( $name, $fromSharedDirectory = false ) {
		$image = null;
		if ( $fromSharedDirectory ) {
			$image = wfFindFile( $name );
		}
		if ( !$image ) {
			$image = wfLocalFile( $name );
		}
		return $image->getUrl();
	}
}

//Alias
class BsCAI extends BsCommonAJAXInterface {}