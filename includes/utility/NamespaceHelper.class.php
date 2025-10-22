<?php

use MediaWiki\Context\RequestContext;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;

class BsNamespaceHelper {

	protected static $namespaceConstants = null;

	/**
	 * returns the constantname for MW NS like 0 => NS_MAIN
	 * @param int $iNamespaceId
	 * @return string
	 */
	public static function getMwNamespaceConstant( $iNamespaceId ) {
		self::getMwNamespaceConstants();
		if ( isset( self::$namespaceConstants[$iNamespaceId] ) ) {
			return self::$namespaceConstants[$iNamespaceId];
		} else {
			return '';
		}
	}

	/**
	 * Returns array with MW NS Mapping
	 * @return array
	 */
	public static function getMwNamespaceConstants() {
		if ( self::$namespaceConstants === null ) {
			$ns = MediaWikiServices::getInstance()
				->getNamespaceInfo()
				->getCanonicalNamespaces();
			foreach ( $ns as $id => &$name ) {
				$name = self::getNamespaceConstName( $id, $name );
			}

			self::$namespaceConstants = $ns + $GLOBALS['bsgSystemNamespaces'];
		}

		return self::$namespaceConstants;
	}

	/**
	 * @param int $iNS
	 * @param string|null $name
	 * @param bool|null $ignoreExisting
	 * @return string
	 */
	public static function getNamespaceConstName( $iNS, $name = null, ?bool $ignoreExisting = false ) {
		// find existing NS_ definitions
		$aNSConstants = [];
		foreach ( get_defined_constants() as $key => $value ) {
			if ( strpos( $key, "NS_" ) === 0
				// ugly solution to identify smw namespaces as they don't adhere to the convention
				|| strpos( $key, "SMW_NS_" ) === 0
				|| strpos( $key, "SF_NS_" ) === 0
				|| strpos( $key, "PF_NS_" ) === 0
			) {
				$aNSConstants[$key] = $value;
			}
		}

		$aNSConstants = array_flip( $aNSConstants );

		// Use existing constant name if possible
		if ( isset( $aNSConstants[$iNS] ) && !$ignoreExisting ) {
			$sConstName = $aNSConstants[$iNS];
		} elseif ( is_string( $name ) && preg_match( "/^[a-zA-Z0-9_]{3,}$/", $name ) ) {
			// If compatible, use namespace name as const name
			$sConstName = 'NS_' . strtoupper( $name );
		} else {
			// Otherwise use namespace number
			$sConstName = 'NS_' . $iNS;
		}

		return $sConstName;
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
		// TODO SW(05.01.12 15:21): Profiling
		if ( $bReturnNamesForMainAndAll ) {
			if ( $iNamespaceId == 0 ) {
				return wfMessage( 'bs-ns_main' )->text();
			}
			if ( $iNamespaceId == -99 ) {
				return wfMessage( 'bs-ns_all' )->text();
			}
		}

		return MediaWikiServices::getInstance()->getContentLanguage()->getNsText(
			$iNamespaceId
		);
	}

	/**
	 * Returns all possible active names and aliases for a given namespace, including localized forms.
	 * @param int $iNamespaceId number of namespace index
	 * @return array List of namespace names
	 */
	public static function getNamespaceNamesAndAliases( $iNamespaceId ) {
		global $wgCanonicalNamespaceNames;
		$names = [];

		// get canonical name
		$names[] = $wgCanonicalNamespaceNames[$iNamespaceId];
		// return localized namespace aliases
		return array_merge( self::getNamespaceAliases( $iNamespaceId ), $names );
	}

	/**
	 * Returns all possible aliases for a given namespace, including localized forms.
	 * @param int $iNamespaceId number of namespace index
	 * @return array List of namespace aliases
	 */
	public static function getNamespaceAliases( $iNamespaceId ) {
		global $wgNamespaceAliases;
		$aAliases = [];

		// get localized namespace name
		$aAliases[] = self::getNamespaceName( $iNamespaceId );
		// get canonical aliases (used for image/file namespace)
		$aAliases = array_merge( array_keys( $wgNamespaceAliases, $iNamespaceId ), $aAliases );
		// get localized aliases
		$aTmpAliases = MediaWikiServices::getInstance()->getContentLanguage()
			->getNamespaceAliases();
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
		if ( is_numeric( $vNamespace ) ) {
			return $vNamespace;
		}

		return MediaWikiServices::getInstance()->getContentLanguage()->getNsIndex( $vNamespace );
	}

	/**
	 *
	 * @param array $aNamespaces Array of namespaces i.e. array( 3, 5, 'SomeNamespace', 4 )
	 * @return array Array of integer Namespaces, i.e. array( 4, 14, 100, 7 );
	 * @throws BsInvalidNamespaceException In case a invalid namespace is given
	 */
	public static function getNamespaceIdsFromAmbiguousArray( $aNamespaces ) {
		return self::getNamespaceIdsFromAmbiguousCSVString( implode( ',', $aNamespaces ) );
	}

	/**
	 * Resolves a given ambigous list of namespaces into a array of integer namespace ids
	 * @param string $sCSV Comma separated list of integer and string namespaces,
	 * i.e. "4, 14, SomeNamespace, 7". The strings "all", "-" and the empty string "" will
	 * result in an array of all available namespaces.
	 * @return array Array of integer Namespaces, i.e. array( 4, 14, 100, 7 );
	 * @throws BsInvalidNamespaceException In case a invalid namespace is given
	 */
	public static function getNamespaceIdsFromAmbiguousCSVString( $sCSV = '' ) {
		if ( !isset( $sCSV ) || !is_string( $sCSV ) ) {
			throw new \MWException(
				__CLASS__ . ":" . __METHOD__ . ' - expects comma separated string'
			);
		}
		$contLang = MediaWikiServices::getInstance()->getContentLanguage();
		$sCSV = trim( $sCSV );
		// make namespaces case insensitive
		$sCSV = mb_strtolower( $sCSV );

		if ( in_array( $sCSV, [ 'all', '-', '' ] ) ) {
			// for compatibility reason the '-' is equivalent to 'all'
			return array_keys( $contLang->getNamespaces() );
		}

		$aAmbiguousNS = explode( ',', $sCSV );
		$aAmbiguousNS = array_map( 'trim', $aAmbiguousNS );
		$aValidNamespaceIntIndexes = [];
		$aInvalidNamespaces = [];

		foreach ( $aAmbiguousNS as $vAmbiguousNS ) {
			if ( is_numeric( $vAmbiguousNS ) ) {
				// Given value is a namespace id.
				if ( $contLang->getNsText( $vAmbiguousNS ) === false ) {
					// Does a namespace with the given id exist?
					$aInvalidNamespaces[] = $vAmbiguousNS;
				} else {
					$aValidNamespaceIntIndexes[] = $vAmbiguousNS;
				}
			} else {
				if ( $vAmbiguousNS == wfMessage( 'bs-ns_main' )->text()
					|| strcmp( $vAmbiguousNS, "main" ) === 0 ) {
					$iNamespaceIdFromText = 0;
				} elseif ( $vAmbiguousNS == '' ) {
					$iNamespaceIdFromText = 0;
				} else {
					// Given value is a namespace text.
					// 'Bluespice talk' -> 'Bluespice_talk'
					$vAmbiguousNS = str_replace( ' ', '_', $vAmbiguousNS );
					// Does a namespace id for the given namespace text exist?
					$iNamespaceIdFromText = $contLang->getNsIndex( $vAmbiguousNS );
				}
				if ( $iNamespaceIdFromText === false ) {
					$aInvalidNamespaces[] = $vAmbiguousNS;
				} else {
					$aValidNamespaceIntIndexes[] = $iNamespaceIdFromText;
				}
			}
		}

		// Does the given CSV list contain any invalid namespaces?
		if ( !empty( $aInvalidNamespaces ) ) {
			$oInvalidNamespaceException = new BsInvalidNamespaceException();
			$oInvalidNamespaceException->setListOfInvalidNamespaces( $aInvalidNamespaces );
			$oInvalidNamespaceException->setListOfValidNamespaces( $aValidNamespaceIntIndexes );
			throw $oInvalidNamespaceException;
		}

		// minify the Array, rearrange indexes and return it
		return array_values( array_unique( $aValidNamespaceIntIndexes ) );
	}

	/**
	 * Creates an array for the HTMLFormField class for select boxes.
	 * @param array $aExcludeIds
	 * @return array
	 */
	public static function getNamespacesForSelectOptions( $aExcludeIds = [] ) {
		$aNamespaces = [];

		$contLang = MediaWikiServices::getInstance()->getContentLanguage();
		foreach ( $contLang->getNamespaces() as $sNamespace ) {
			$iNsIndex = $contLang->getNsIndex( $sNamespace );
			if ( in_array( $iNsIndex, $aExcludeIds ) ) {
				// Filter namespaces
				continue;
			}
			$aNamespaces[$iNsIndex] = self::getNamespaceName( $iNsIndex );
		}
		return $aNamespaces;
	}

	/**
	 * Checks if a user has a given permission on a given namepsace
	 * @param int $iID namespace
	 * @param string $sPermission permission
	 * @return bool returns true if user is allowed , otherwise false
	 */
	public static function checkNamespacePermission( $iID, $sPermission ) {
		if ( !is_numeric( $iID ) ) {
			return false;
		}

		$oDummyTitle = Title::makeTitle( $iID, 'Dummy' );
		return MediaWikiServices::getInstance()
			->getPermissionManager()
			->userCan(
				$sPermission,
				RequestContext::getMain()->getUser(),
				$oDummyTitle
			);
	}
}
