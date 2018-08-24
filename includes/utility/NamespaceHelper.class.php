<?php

class BsNamespaceHelper {

	protected static $aNamespaceMap = array(
		-2 => 'NS_MEDIA',
		-1 => 'NS_SPECIAL',
		0 => 'NS_MAIN',
		1 => 'NS_TALK',
		2 => 'NS_USER',
		3 => 'NS_USER_TALK',
		4 => 'NS_PROJECT',
		5 => 'NS_PROJECT_TALK',
		6 => 'NS_FILE',
		7 => 'NS_FILE_TALK',
		8 => 'NS_MEDIAWIKI',
		9 => 'NS_MEDIAWIKI_TALK',
		10 => 'NS_TEMPLATE',
		11 => 'NS_TEMPLATE_TALK',
		12 => 'NS_HELP',
		13 => 'NS_HELP_TALK',
		14 => 'NS_CATEGORY',
		15 => 'NS_CATEGORY_TALK'
	);

	/**
	 * returns the constantname for MW NS like 0 => NS_MAIN
	 * @param type $iNamespaceId
	 * @return string 
	 */	
	public static function getMwNamespaceConstant( $iNamespaceId ) {
		if( isset( self::$aNamespaceMap[$iNamespaceId] ) ) {
			return self::$aNamespaceMap[$iNamespaceId];
		} else {
			return '';
		}
	}

	/**
	 * Returns array with MW NS Mapping
	 * @return Array 
	 */
	public static function getMwNamespaceConstants() {
		//TODO: Use this logic for contants mapping
		/*
		$aConsts = get_defined_constants(true);
		foreach($aConsts['user'] as $sContantName => $sContantValue ) {
			if(substr($sContantName, 0, 3) != 'NS_' ) continue;
			//$sContantValue -> $sContantName;
		}
		*/
		global $bsgSystemNamespaces;
		return self::$aNamespaceMap + $bsgSystemNamespaces;
	}

	/**
	 * Maps namespace-id to namespace-name (i18n'ed)
	 * array (
	 *    -2 => 'Media',
	 *    -1 => 'Spezial',
	 *    0 => '',
	 *    1 => 'Diskussion',
	 *    2 => 'Benutzer',
	 *    3 => 'Benutzer_Diskussion',
	 *    4 => 'blue_spice',
	 *    5 => 'blue_spice_Diskussion',
	 *    6 => 'Datei',
	 *    7 => 'Datei_Diskussion',
	 *    8 => 'MediaWiki',
	 *    9 => 'MediaWiki_Diskussion',
	 *    10 => 'Vorlage',
	 *    11 => 'Vorlage_Diskussion',
	 *    12 => 'Hilfe',
	 *    13 => 'Hilfe_Diskussion',
	 *    14 => 'Kategorie',
	 *    15 => 'Kategorie_Diskussion',
	 *    102 => 'Blog',
	 *    103 => 'Blog_talk'
	 * )
	 * @param int $iNamespaceId namespace-id/-number
	 * @param bool $bReturnNamesForMainAndAll
	 * @return string name of namespace internationalized
	 */
	public static function getNamespaceName( $iNamespaceId, $bReturnNamesForMainAndAll = true ) {
		global $wgContLang;
		// TODO SW(05.01.12 15:21): Profiling
		if ( $bReturnNamesForMainAndAll ) {
			if ( $iNamespaceId == 0 ) return wfMessage( 'bs-ns_main' )->plain();
			if ( $iNamespaceId == -99 ) return wfMessage( 'bs-ns_all' )->plain();
		}
		return $wgContLang->getNsText( $iNamespaceId );
	}

	/**
	 * Returns all possible active names and aliases for a given namespace, including localized forms.
	 * @global Language $wgContLang MediaWiki object for content language
	 * @global array $wgNamespaceAliases stores all the namespace aliases
	 * @global array $wgCanonicalNamespaceNames stores generic namespace names
	 * @param int $iNamespaceId number of namespace index
	 * @return array List of namespace names 
	 */
	public static function getNamespaceNamesAndAliases($iNamespaceId) {
		global $wgContLang, $wgNamespaceAliases, $wgCanonicalNamespaceNames;
		$aAliases = array();

		// get canonical name
		$aAliases[] = $wgCanonicalNamespaceNames[$iNamespaceId];
		// get localized namespace name
		$aAliases[] = self::getNamespaceName( $iNamespaceId );
		// get canonical aliases (used for image/file namespace)
		$aAliases = array_merge( array_keys( $wgNamespaceAliases, $iNamespaceId ), $aAliases );
		// get localized aliases
		$aTmpAliases = $wgContLang->getNamespaceAliases();
		if ( $aTmpAliases ) {
			$aAliases = array_merge( array_keys( $aTmpAliases, $iNamespaceId ), $aAliases );
		}

		return $aAliases;
	}

	/**
	 * Determines a integer id for a given ambiguous value.
	 * @param mixed $vNamespace Integer id or string name
	 * @return int The  NamespaceId.
	 */
	public static function getNamespaceIndex( $vNamespace ) {
		if ( is_numeric( $vNamespace ) ) return $vNamespace;
		global $wgContLang;

		return $wgContLang->getNsIndex( $vNamespace );
	}

	/**
	 *
	 * @param array $aNamespaces Array of namespaces i.e. array( 3, 5, 'SomeNamespace', 4 )
	 * @return Array Array of integer Namespaces, i.e. array( 4, 14, 100, 7 );
	 * @throws BsInvalidNamespaceException In case a invalid namespace is given 
	 */
	public static function getNamespaceIdsFromAmbiguousArray($aNamespaces) {
		return self::getNamespaceIdsFromAmbiguousCSVString( implode( ',', $aNamespaces ) );
	}

	/**
	 * Resolves a given ambigous list of namespaces into a array of integer namespace ids
	 * @param string $sCSV Comma separated list of integer and string namespaces, i.e. "4, 14, SomeNamespace, 7". The strings "all", "-" and the empty string "" will result in an array of all available namespaces.
	 * @return array Array of integer Namespaces, i.e. array( 4, 14, 100, 7 );
	 * @throws BsInvalidNamespaceException In case a invalid namespace is given
	 */
	public static function getNamespaceIdsFromAmbiguousCSVString( $sCSV = '' ) {
		if( !isset( $sCSV ) || !is_string( $sCSV ) ) {
			throw new \MWException(
				__CLASS__.":".__METHOD__.' - expects comma separated string'
			);
		}
		global $wgContLang;
		$sCSV = trim( $sCSV );
		$sCSV = mb_strtolower( $sCSV ); // make namespaces case insensitive

		if ( in_array( $sCSV, array( 'all', '-', '' ) ) ) { //for compatibility reason the '-' is equivalent to 'all'
			return array_keys( $wgContLang->getNamespaces() );
		}

		$aAmbiguousNS = explode( ',', $sCSV );
		$aAmbiguousNS = array_map( 'trim', $aAmbiguousNS );
		$aValidNamespaceIntIndexes = array();
		$aInvalidNamespaces = array();

		foreach ( $aAmbiguousNS as $vAmbiguousNS ) {
			if ( is_numeric( $vAmbiguousNS ) ) { //Given value is a namespace id.
				if ( $wgContLang->getNsText( $vAmbiguousNS ) === false ) { //Does a namespace with the given id exist?
					$aInvalidNamespaces[] = $vAmbiguousNS;
				} else {
					$aValidNamespaceIntIndexes[] = $vAmbiguousNS;
				}
			} else {
				if ( $vAmbiguousNS == wfMessage( 'bs-ns_main' )->plain()
					|| strcmp( $vAmbiguousNS, "main" ) === 0 ) {
					$iNamespaceIdFromText = 0;
				} else if ( $vAmbiguousNS == '' ) {
					$iNamespaceIdFromText = 0;
				} else {
					//Given value is a namespace text.
					$vAmbiguousNS = str_replace( ' ', '_', $vAmbiguousNS ); //'Bluespice talk' -> 'Bluespice_talk'
					//Does a namespace id for the given namespace text exist?
					$iNamespaceIdFromText = $wgContLang->getNsIndex( $vAmbiguousNS );
				}
				if ( $iNamespaceIdFromText === false ) {
					$aInvalidNamespaces[] = $vAmbiguousNS;
				} else {
					$aValidNamespaceIntIndexes[] = $iNamespaceIdFromText;
				}
			}
		}

		//Does the given CSV list contain any invalid namespaces?
		if ( !empty( $aInvalidNamespaces ) ) {
			$oInvalidNamespaceException = new BsInvalidNamespaceException();
			$oInvalidNamespaceException->setListOfInvalidNamespaces( $aInvalidNamespaces );
			$oInvalidNamespaceException->setListOfValidNamespaces( $aValidNamespaceIntIndexes );
			throw $oInvalidNamespaceException;
		}

		return array_values( array_unique( $aValidNamespaceIntIndexes ) ); //minify the Array, rearrange indexes and return it
	}

	/**
	 * Creates an array for the HTMLFormField class for select boxes.
	 * @global Language $wgContLang
	 * @param array $aExcludeIds
	 * @return array 
	 */
	public static function getNamespacesForSelectOptions( $aExcludeIds = array() ) {
		global $wgContLang;
		$aNamespaces = array();

		foreach ( $wgContLang->getNamespaces() as $sNamespace ) {
			$iNsIndex = $wgContLang->getNsIndex( $sNamespace );
			if ( in_array( $iNsIndex, $aExcludeIds ) ) continue; //Filter namespaces
			$aNamespaces[$iNsIndex] = self::getNamespaceName( $iNsIndex );
		}
		return $aNamespaces;
	}

	/**
	 * Checks if a user has a given permission on a given namepsace
	 * @param Integer $iID namespace
	 * @param String $sPermission permission
	 * @return boolean returns true if user is allowed , otherwise false
	 */
	public static function checkNamespacePermission( $iID, $sPermission ) {
		if ( !is_numeric( $iID ) ) return false;

		$oDummyTitle = Title::makeTitle( $iID, 'Dummy' );
		if ( $oDummyTitle->userCan( $sPermission ) ) {
			return true;
		} else {
			return false;
		}
	}
}
