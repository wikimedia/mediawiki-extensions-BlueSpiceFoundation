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