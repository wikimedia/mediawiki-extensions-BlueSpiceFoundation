<?php

class BsCommonAJAXInterface {

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
		if ( !$image ) {
			return $image;
		}
		return $image->getUrl();
	}
}

//Alias
class BsCAI extends BsCommonAJAXInterface {}