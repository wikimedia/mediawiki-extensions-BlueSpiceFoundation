<?php

//index.php?action=ajax&rs=BSCommonAJAXInterface::getTitleStoreData&rsargs[]={}
//index.php?action=ajax&rs=BSCommonAJAXInterface::getNamespaceStoreData&rsargs[]={}

class BsCommonAJAXInterface {
	public static function getTitleStoreData( $sOptions = '{}' ) {
		//TODO: Reflect $options ans WebRequest::getVal('start|limit|...')
		$aOptions = FormatJson::decode($sOptions, true);
		
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
	
	public static function getNamespaceStoreData( $sOptions = '{}' ) {
		//TODO: Reflect $options ans WebRequest::getVal('start|limit|...')
		$aOptions = FormatJson::decode($sOptions, true);

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
	
	public static function getUserStoreData( $sOptions = '{}' ) {
		$aDefaultOptions = array(
			'group' => '', //TODO: make array? How to operate them? AND / OR
			'permission' => '', //TODO: see above
		);
		
		$aOptions = FormatJson::decode($sOptions, true);
		//TODO: recursive merge with $aDefaultOptions

		$oResult = new stdClass();
		$oResult->users = array();
		
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
			'user',
			array( 'user_id', 'user_name', 'user_real_name' )
		);
		
		foreach( $res as $row ) {
			$oUser = User::newFromId( $row->user_id );
			$oUserData = new stdClass();
			
			//DB fields
			$oUserData->user_id = (int)$row->user_id;
			$oUserData->user_name = $row->user_name;
			
			//Calculated fields
			$oUserData->display_name = BsCore::getUserDisplayName( $oUser );
			$oUserData->page_prefixed_text = $oUser->getUserPage()->getPrefixedText();
			
			$oResult->users[] = $oUserData;
		}

		return FormatJson::encode($oResult);
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
				// Tables
				array( 'page', 'categorylinks' ),
				// Fields
				array( 'page_title AS cat_title', 'cl_to AS parent_title' ),
				// Conditions
				array( 'page_namespace' => NS_CATEGORY),
				__METHOD__,
				// Options
				array( 'ORDER BY page_title' ),
				// Joins
				array( 'categorylinks' => array( 'JOIN', 'page_id = cl_from' ), )
			);

			foreach( $resSubcats as $row ) {
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
				'category',
					'*',
					'cat_title NOT IN (\'' . implode( '\', \'', $aSubCategories ) . '\')'
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
			$aJoinConds = array( 'categorylinks' => array( 'JOIN', 'page_id=cl_from') );

			$resSubCategories = $dbr->select( 
				$aTables,
				$aFields,
				$aConditions,
				$sMethod,
				$aOptions,
				$aJoinConds
			);

			$aSubCategories = array();
			
			foreach( $resSubCategories as $row ) {
				$aSubCategories[] = $row->page_title;
			}
			asort( $aSubCategories );
			
			foreach( $aSubCategories as $sCategory ) {
				$oTmpCat = Category::newFromName( $sCategory );
				$oCategory = new stdClass();
				$oCategory->text = str_replace( '_', ' ', $oTmpCat->getName() );
				$oCategory->leaf = ( $oTmpCat->getSubcatCount() > 0 ) ? false : true;
				$oCategory->id = $sNode.'/'.str_replace('/', '+', $oCategory->text );
				$aResult[] = $oCategory;
			}
		}

		return FormatJson::encode($aResult);
	}
	
	public static function getCategoryStoreData( $sOptions = '{}' ) {
		// $sOptions will be used... maybe
		$oResult = new stdClass();
		$oResult->categories = array();

		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
			'category',
			array( 'cat_id', 'cat_title', 'cat_pages', 'cat_subcats', 'cat_files' )
		);

		foreach( $res as $row ) {
			$oCategoryData = new stdClass();

			$oCategoryData->cat_id = (int)$row->cat_id;
			$oCategoryData->cat_title = $row->cat_title;
			$oCategoryData->text = $row->cat_title;
			$oCategoryData->cat_pages = (int)$row->cat_pages;
			$oCategoryData->cat_subcats = (int)$row->cat_subcats;
			$oCategoryData->cat_files = (int)$row->cat_files;

			$oCategoryData->prefixed_text = Title::newFromText( $row->cat_title, NS_CATEGORY )->getPrefixedText();

			$oResult->categories[] = $oCategoryData;
		}

		return FormatJson::encode($oResult);
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
		if( $fromSharedDirectory ) {
			$image = wfFindFile( $name );
		}
		if( !$image ) {
			$image = wfLocalFile( $name );
		}
		return $image->getUrl();
	}
}

//Alias
class BsCAI extends BsCommonAJAXInterface {}