<?php
/**
 * This is the BsAdapter prototype.
 * 
 * Every adapter class have to be inherited from this prototype.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * 
 * This file is part of BlueSpice for MediaWiki
 * For further information visit http://www.blue-spice.org
 *
 * @author     Sebastian Ulbricht <sebastian.ulbricht@dragon-design.hk>
 * @author     Robert Vogel <vogel@hallowelt.biz>
 * @version    0.1.0
 * @version    $Id: Adapter.class.php 9895 2013-06-24 14:53:47Z rvogel $
 * @package    Bluespice_Core
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * the BsAdapter prototype class
 * @package BlueSpice_Core
 * @subpackage Core
 */
abstract class BsAdapter {
	/**
	 * holds the name and the filepath of every registered adapter as an assoziative array
	 * @var array
	 * @access protected
	 */
	protected static $prRegisteredAdapters = array();
	/**
	 * holds the requested URI after the first time, the method getRequestURI was running
	 * @var string
	 */
	protected static $prRequestUri = null;
	/**
	 * a state flag if the requested URL is encodet
	 * @var bool
	 */
	protected static $prUrlIsEncoded = false;

	/**
	 * Use this method to register a new adapter.
	 * 
	 * The adapter class has to be inherited from the BsAdapter class and the classname
	 * has to match the form "BsAdapter<the given name>". So if the name of the adapter
	 * is Test, the classname would be BsAdapterTest.
	 * 
	 * @param string $sName the adapter name
	 * @param string $sPath the path to the adapters classfile
	 */
	public static function registerAdapter($sName, $sPath) {
		self::$prRegisteredAdapters[$sName] = $sPath;
	}

	/**
	 * This method loads the adapter with the given name and returns an instance of it.
	 * 
	 * @param string $sName the adapter name
	 * @return object a instance of the adapter class
	 */
	public static function loadAdapter($sName) {
		wfProfileIn( 'BS::'.__METHOD__ );
		if(!isset(self::$prRegisteredAdapters[$sName])) {
			throw new Exception('GIVEN_ADAPTER_NOT_FOUND', 1000);
		}
		$class = 'BsAdapter'.$sName;
		$instance = new $class();
		wfProfileOut( 'BS::'.__METHOD__ );
		return $instance;
	}

	/**
	 * Determines the request URI for Apache and IIS
	 * 
	 * @param bool $getUrlEncoded set to true to get URI url encoded
	 * @return string the requested URI
	 */
	public static function getRequestURI($getUrlEncoded = false) {
		wfProfileIn( 'BS::'.__METHOD__ );
		if (self::$prRequestUri === null) {
			$requestUri = '';
			if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
				$requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
			} elseif (isset($_SERVER['REQUEST_URI'])) {
				$requestUri = $_SERVER['REQUEST_URI'];
			} elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
				$requestUri = $_SERVER['ORIG_PATH_INFO'];
				if (!empty($_SERVER['QUERY_STRING'])) {
					$requestUri .= '?' . $_SERVER['QUERY_STRING'];
				}
			}
			self::$prRequestUri = $requestUri;
			self::$prUrlIsEncoded = (urldecode(self::$prRequestUri) != self::$prRequestUri);
		}
		if($getUrlEncoded) {
			return (self::$prUrlIsEncoded ? self::$prRequestUri : urlencode(self::$prRequestUri));
		}
		wfProfileOut( 'BS::'.__METHOD__ );
		return (self::$prUrlIsEncoded ? urldecode(self::$prRequestUri) : self::$prRequestUri);
	}

	/**
	 * This method spends a possibility to get the name of variables in an adapter specific form.
	 * @param string $sName the name of the variable
	 * @param bool $bSearchGlobals true to search for the variable in global scope
	 * @return string the variable name in adapter specific form (with pre-/postfix or something similar)
	 */
	public abstract function getVarName($name, $global = false);

	/**
	 * setup the adapter
	 */
	public abstract function doInitialise();
}


/**
 * BlueSpice for MediaWiki
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice for MediaWiki
 * For further information visit http://www.blue-spice.org
 *
 * @author     Markus Glaser <glaser@hallowelt.biz>
 * @author     Sebastian Ulbricht
 * @version    1.0.0
 * @version    $Id: Adapter.class.php 9895 2013-06-24 14:53:47Z rvogel $
 * @package    BlueSpice_MW
 * @subpackage Adapter
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */
/* Changelog
 * v1.0.0
 * - Added some documentation
 * - Added userpagesettings event
 * - Refactoring
 * - Raised to stable
 * v0.1.1
 * - added addEditButton
 * v0.1.0
 * - inital release
 */

// last Code Review 2011-06-23, RBV

/**
 * The MediaWiki Adapter class
 * @package BlueSpice_Adapters
 * @subpackage BlueSpice_AdapterMW
 */
class BsAdapterMW extends BsAdapter {

	protected static $prIsSpecial = null;
	protected static $prIsCategory = null;
	protected static $prIsFile = null;
	protected static $prAction = null;
	protected static $prCurrentUser = null;

	public static function loadCurrentUser() {
		// TODO SW(05.01.12 15:22): Profiling
		/* Load current user */
		global $wgUser;

		if (!$wgUser || is_null($wgUser->mId)) {

			if (!is_null(self::$prCurrentUser))
				return self::$prCurrentUser;

			self::$prCurrentUser = User::newFromSession();
			self::$prCurrentUser->load();
			return self::$prCurrentUser;
		}

		return $wgUser;
		// Used to bie like the following code. however, this did not take into account the __session-Cookie, and logged out users were still recognized.
		/* if( isset( $_SESSION['wsUserID'] ) ) {
		  self::$prCurrentUser = User::newFromId( $_SESSION['wsUserID'] ); // object created but not loaded from DB
		  self::$prCurrentUser->loadFromId(); // get from DB or MemCache
		  return self::$prCurrentUser;
		  }
		  return new User(); //anonymous
		 */
	}

	public static function getUserDisplayName($oUser = null) {
		// TODO SW(09.01.12 08:57): Profiling
		//wfProfileIn( 'BS::'.__METHOD__ );
		if ($oUser === null) {
			$oUser = BsCore::getInstance('MW')->getAdapter()->User;
			//wfProfileIn( 'BS::'.__METHOD__ );
		}
		if (!( $oUser instanceof User )) {
			//wfProfileOut( 'BS::'.__METHOD__ );
			return false;
		}
		$sRealname = $oUser->getRealName();
		if ($sRealname) {
			//wfProfileOut( 'BS::'.__METHOD__ );
			return $sRealname;
		} else {
			//wfProfileOut( 'BS::'.__METHOD__ );
			return $oUser->getName();
		}
	}
	
	//<editor-fold desc="Deprecated MWNamespace related methods" defaultstate="collapsed">
	/**
	 * returns the constantname for MW NS like 0 => NS_MAIN
	 * @param type $iNamespaceId
	 * @return string 
	 * @deprecated since version 1.22 Use BsNamespaceHelper::getMwNamespaceConstant() instead
	 */	
	public static function getMwNamespaceConstant( $iNamespaceId ) {
		MWDebug::deprecated(__METHOD__);
		return BsNamespaceHelper::getMwNamespaceConstant($iNamespaceId);
	}
	
	/**
	 * returns array with MW NS Mapping
	 * @return Array 
	 * @deprecated since version 1.22 Use BsNamespaceHelper::getMwNamespaceConstants() instead
	 */
	public static function getMwNamespaceConstants() {
		MWDebug::deprecated(__METHOD__);
		return BsNamespaceHelper::getMwNamespaceConstants();
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
	 * @deprecated since version 1.22 Use BsNamespaceHelper::getNamespaceName() instead
	 */
	public static function getNamespaceName($iNamespaceId, $bReturnNamesForMainAndAll = true) {
		MWDebug::deprecated(__METHOD__);
		return BsNamespaceHelper::getNamespaceName($iNamespaceId, $bReturnNamesForMainAndAll);
	}

	/**
	 * Returns all possible active names and aliases for a given namespace, including localized forms.
	 * @global Language $wgContLang MediaWiki object for content language
	 * @global array $wgNamespaceAliases stores all the namespace aliases
	 * @global array $wgCanonicalNamespaceNames stores generic namespace names
	 * @param int $iNamespaceId number of namespace index
	 * @return array List of namespace names 
	 * @deprecated since version 1.22 Use BsNamespaceHelper::getNamespaceNamesAndAliases() instead
	 */
	public static function getNamespaceNamesAndAliases($iNamespaceId) {
		MWDebug::deprecated(__METHOD__);
		wfProfileIn('BS::' . __METHOD__);
		$aAliases = BsNamespaceHelper::getNamespaceNamesAndAliases($iNamespaceId);
		wfProfileOut('BS::' . __METHOD__);
		return $aAliases;
	}

	/**
	 * Determines a integer id for a given ambiguous value.
	 * @param mixed $vNamespace Integer id or string name
	 * @return int The NamespaceId.
	 * @deprecated since version 1.22 Use BsNamespaceHelper::getNamespaceIndex() instead
	 */
	public static function getNamespaceIndex($vNamespace) {
		MWDebug::deprecated(__METHOD__);
		wfProfileIn('BS::' . __METHOD__);
		$iIndex = BsNamespaceHelper::getNamespaceIndex($vNamespace);
		wfProfileOut('BS::' . __METHOD__);
		return $iIndex;
	}

	/**
	 *
	 * @param array $aNamespaces Array of namespaces i.e. array( 3, 5, 'SomeNamespace', 4 )
	 * @return Array Array of integer Namespaces, i.e. array( 4, 14, 100, 7 );
	 * @throws BsInvalidNamespaceException In case a invalid namespace is given 
	 * @deprecated since version 1.22 Use BsNamespaceHelper::getNamespaceIdsFromAmbiguousArray() instead
	 */
	public static function getNamespaceIdsFromAmbiguousArray($aNamespaces) {
		MWDebug::deprecated(__METHOD__);
		return BsNamespaceHelper::getNamespaceIdsFromAmbiguousArray($aNamespaces);
	}

	/**
	 * Resolves a given ambigous list of namespaces into a array of integer namespace ids
	 * @param string $sCSV Comma seperated list of integer and string namespaces, i.e. "4, 14, SomeNamespace, 7". The strings "all", "-" and the empty string "" will result in an array of all available namespaces.
	 * @return array Array of integer Namespaces, i.e. array( 4, 14, 100, 7 );
	 * @throws BsInvalidNamespaceException In case a invalid namespace is given
	 * @deprecated since version 1.22 Use BsNamespaceHelper::getNamespaceIdsFromAmbiguousCSVString() instead
	 */
	public static function getNamespaceIdsFromAmbiguousCSVString($sCSV = '') {
		MWDebug::deprecated(__METHOD__);
		wfProfileIn('BS::' . __METHOD__);
		$aValidNamespaceIntIndexes = BsNamespaceHelper::getNamespaceIdsFromAmbiguousCSVString($sCSV);
		wfProfileOut('BS::' . __METHOD__);
		return $aValidNamespaceIntIndexes;
	}

	/**
	 * Creates an array for the HTMLFormField class for select boxes.
	 * @global Language $wgContLang
	 * @param array $aExcludeIds
	 * @return array 
	 * @deprecated since version 1.22 Use BsNamespaceHelper::getNamespacesForSelectOptions() instead
	 */
	public static function getNamespacesForSelectOptions($aExcludeIds = array()) {
		MWDebug::deprecated(__METHOD__);
		wfProfileIn('BS::' . __METHOD__);
		$aNamespaces = BsNamespaceHelper::getNamespacesForSelectOptions($aExcludeIds);
		wfProfileOut('BS::' . __METHOD__);
		return $aNamespaces;
	}
	//</editor-fold>

	/**
	 * Wrapper for wfTimestamp()
	 * @return string The current timestamp.
	 * @deprecated since version 1.22 Use wfTimestamp() instead
	 */
	public static function getTimestamp() {
		MWDebug::deprecated(__METHOD__);
		return wfTimestamp();
	}

	// TODO MRG (09.12.10 11:21): Habe silent im Standard auf true gesetzt. Echo ist ohnehin nicht gut.
	/**
	 *
	 * @param string $sPermission
	 * @param string $sI18NInstanceKey
	 * @param string $sI18NMessageKey
	 * @param bool $bSilent
	 * @return bool Always false
	 */
	public static function checkAccessAdmission($sPermission = 'read', $sI18NInstanceKey = 'BlueSpice', $sI18NMessageKey = 'not_allowed', $bSilent = true) {
		// TODO SW(05.01.12 15:26): Profiling
		// TODO MRG28072010: isAllowed prüft nicht gegen die Artikel. D.H. die Rechte sind nicht per Namespace überprüfbar
		$oUser = self::loadCurrentUser();
		if ($oUser->isAllowed($sPermission))
			return true;
		if (!$bSilent)
			echo wfMsg('bs-' . $sI18NMessageKey);
		return false;
	}

	// CR RBV (23.06.11 13:37): Das erscheint mir recht hacky zu sein... Hier wird die URI geparst. Wieso?
	/**
	 * Parse URI for keyword 'Special' (i80n - ready)
	 * @return mixed false if not found or string containing the given name of the specialpage
	 * @deprecated since version 1.22 Use current Contexts' Title object
	 */
	public static function isSpecial() {
		MWDebug::deprecated(__METHOD__);
		// TODO SW(05.01.12 15:26): Profiling
		if (self::$prIsSpecial !== null)
			return self::$prIsSpecial;
		$requestURI = BsAdapter::getRequestURI();

		global $wgLanguageCode;
		if ($wgLanguageCode == 'de-formal' || $wgLanguageCode == 'de') {
			$sNsSpecial = 'Spezial';
		} else {
			$sNsSpecial = 'Special';
		}
		$keyWord = 'Special:';
		$iS = strpos($requestURI, $keyWord);
		if (!$iS) {
			$keyWord = 'Special=';
			$iS = strpos($requestURI, $keyWord);
		}
		if (!$iS) {
			$keyWord = $sNsSpecial . ':';
			$iS = strpos($requestURI, $keyWord);
		}
		if (!$iS) {
			$keyWord = $sNsSpecial . '=';
			$iS = strpos($requestURI, $keyWord);
		}
		if ($iS) { // ( $iS===false ) or ( $iS contains the position of $keyWord in BsAdapter::getRequestURI() )
			//TODO: does it work with cleaned URIs and IIS ??
			$iS = substr($requestURI, $iS + strlen($keyWord), strlen($requestURI) - 1);
			$iS = strtok($iS, "?");
			$iS = strtok($iS, "&");
			$iS = strtok($iS, "#");
			$iS = strtok($iS, "/");
		}
		self::$prIsSpecial = $iS;
		return self::$prIsSpecial;
	}

	// CR RBV (23.06.11 13:38): siehe CR zu isSpecial()
	/**
	 * Parse URI for keyword 'Category' (i80n - ready)
	 * @return mixed false if not found or string containing the given name of the specialpage
	 * @deprecated since version 1.22 Use current Contexts' Title object
	 */
	public static function isCategory() {
		MWDebug::deprecated(__METHOD__);
		// TODO SW(05.01.12 15:26): Profiling
		if (self::$prIsCategory !== null)
			return self::$prIsCategory;

		$requestURI = BsAdapter::getRequestURI();
		$keyWord = 'Category:';

		$iS = strpos($requestURI, $keyWord);

		if (!$iS) {
			$keyWord = 'Category=';
			$iS = strpos($requestURI, $keyWord);
		}

		if (!$iS) {
			$keyWord = wfMsg('bs-ns_category') . ':';
			$iS = strpos($requestURI, $keyWord);
		}

		if (!$iS) {
			$keyWord = wfMsg('bs-ns_category') . '=';
			$iS = strpos($requestURI, $keyWord);
		}

		if ($iS) { // ( $iS===false ) or ( $iS contains the position of $keyWord in BsAdapter::getRequestURI() )
			//TODO: does it work with cleaned URIs and IIS ??
			$iS = substr($requestURI, $iS + strlen($keyWord), strlen($requestURI) - 1);
			$iS = strtok($iS, "?");
			$iS = strtok($iS, "&");
			$iS = strtok($iS, "#");
			$iS = strtok($iS, "/");
		}

		self::$prIsCategory = $iS;

		return self::$prIsCategory;
	}

	// CR RBV (23.06.11 13:38): siehe CR zu isSpecial()
	/**
	 * Parse URI for keyword 'File' (i80n - ready)
	 * @return mixed false if not found or string containing the given name of the specialpage
	 * @deprecated since version 1.22 Use current Contexts' Title object
	 */
	public static function isFile() {
		MWDebug::deprecated(__METHOD__);
		// TODO SW(05.01.12 15:27): Profiling
		if (self::$prIsFile !== null)
			return self::$prIsFile;

		$requestURI = BsAdapter::getRequestURI();
		$keyWord = 'File:';

		$iS = strpos($requestURI, $keyWord);

		if (!$iS) {
			$keyWord = 'File=';
			$iS = strpos($requestURI, $keyWord);
		}

		if (!$iS) {
			$keyWord = wfMsg('bs-ns_file') . ':';
			$iS = strpos($requestURI, $keyWord);
		}

		if (!$iS) {
			$keyWord = wfMsg('bs-ns_file') . '=';
			$iS = strpos($requestURI, $keyWord);
		}

		if ($iS) { // ( $iS===false ) or ( $iS contains the position of $keyWord in BsAdapter::getRequestURI() )
			//TODO: does it work with cleaned URIs and IIS ??
			$iS = substr($requestURI, $iS + strlen($keyWord), strlen($requestURI) - 1);
			$iS = strtok($iS, "?");
			$iS = strtok($iS, "&");
			$iS = strtok($iS, "#");
			$iS = strtok($iS, "/");
		}

		self::$prIsFile = $iS;

		return self::$prIsFile;
	}

	public static function getAction() {
		// TODO SW(05.01.12 15:27): Profiling
		if (self::$prAction === null)
			self::$prAction = BsCore::getParam('action', 'view', BsPARAM::REQUEST | BsPARAMTYPE::STRING);
		return self::$prAction;
	}

	/**
	 * Returns the filesystempath of the adapter
	 * @return String Filesystempath of the adapter
	 */
	public static function getFileSystemPath() {
		return dirname(__FILE__);
	}

	/**
	 * Returns the MediaWiki include path variable
	 * @global String $IP MediaWiki include path variable
	 * @return String MediaWiki include path variable
	 */
	public static function getMediaWikiIncludePath() {
		wfProfileIn('BS::' . __METHOD__);
		global $IP;
		wfProfileOut('BS::' . __METHOD__);
		return str_replace('\\', '/', $IP);
	}

	/**
	 * Returns the filesystempath to the webroot directory in which MediaWiki is installed.
	 * @global String $wgScriptPath The relative path from the webroot for hyperlinks.
	 * @return String Webroot directory in which MediaWiki is installed
	 */
	public static function getMediaWikiWebrootPath() {
		global $wgScriptPath;
		return str_replace($wgScriptPath, '', self::getMediaWikiIncludePath());
	}

	protected $mRunlevel = null;
	protected $aEditButtons = array();
	protected $aEditButtonRanking = array();
	public $aBehaviorSwitches = null;
	protected $bUserSettingsLoaded = false;
	protected $oLocalParser = false;
	protected $bMWgte116Initialized = false; // Has the user for MW116 and above already been initialized

	/**
	 * Contructor of the Adapter class
	 */
	public function __construct() {
		wfProfileIn('BS::' . __METHOD__);
		$this->registerAdapterVariables();
		BsConfig::set('CORE::BlueSpiceAdapterPath', defined('WIKI_FARMING_INSTANCE_ROOT') ? WIKI_FARMING_INSTANCE_ROOT.DS.'bluespice-mw' : dirname(__FILE__).'/../../../bluespice-mw');
		
		global $wgAjaxExportList;
		$wgAjaxExportList[] = 'BsAdapterMW::ajaxBSPing';

		wfProfileOut('BS::' . __METHOD__);
	}

	protected function registerAdapterVariables() {
		wfProfileIn('BS::' . __METHOD__);

		global $wgStylePath, $wgScriptPath;
		$sStylePath = $wgStylePath ? $wgStylePath : $wgScriptPath . '/skins/BlueSpiceSkin';

		BsConfig::registerVar('MW::CanonicalNamespaceNames', array(), BsConfig::LEVEL_ADAPTER);
		BsConfig::registerVar('MW::LanguageNames', array(), BsConfig::LEVEL_ADAPTER);
		BsConfig::registerVar('MW::ScriptPath', '', BsConfig::LEVEL_ADAPTER);
		BsConfig::registerVar('MW::FileExtensions', array('doc', 'docx', 'pdf', 'xls'), BsConfig::LEVEL_PUBLIC | BsConfig::RENDER_AS_JAVASCRIPT | BsConfig::TYPE_ARRAY_STRING, 'bs-pref-FileExtensions', 'multiselectplusadd');
		BsConfig::registerVar('MW::ImageExtensions', array('png', 'gif', 'jpg', 'jpeg', 'PNG', 'GIF', 'JPG', 'JPEG'), BsConfig::LEVEL_PUBLIC | BsConfig::RENDER_AS_JAVASCRIPT | BsConfig::TYPE_ARRAY_STRING, 'bs-pref-ImageExtensions', 'multiselectplusadd');
		BsConfig::registerVar('MW::LogoPath', $sStylePath . '/BlueSpiceSkin/bluespice/bs-logo.png', BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_STRING, 'bs-pref-LogoPath');
		BsConfig::registerVar('MW::FaviconPath', $sStylePath . '/BlueSpiceSkin/bluespice/favicon.ico', BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_STRING, 'bs-pref-FaviconPath');
		BsConfig::registerVar('MW::DefaultUserImage', $sStylePath . '/BlueSpiceSkin/bluespice/bs-user-default-image.png', BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_STRING, 'bs-pref-DefaultUserImage');
		BsConfig::registerVar('MW::MiniProfileEnforceHeight', true, BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_BOOL, 'bs-pref-MiniProfileEnforceHeight', 'toggle');
		BsConfig::registerVar('MW::AnonUserImage', $sStylePath . '/BlueSpiceSkin/bluespice/bs-user-anon-image.png', BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_STRING, 'bs-pref-AnonUserImage');
		BsConfig::registerVar('MW::RekursionBreakLevel', 20, BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_INT, 'bs-pref-RekursionBreakLevel');
		BsConfig::registerVar('MW::UserImage', '', BsConfig::LEVEL_USER | BsConfig::TYPE_STRING | BsConfig::NO_DEFAULT, 'bs-authors-pref-UserImage');
		BsConfig::registerVar('MW::PingInterval', 2, BsConfig::LEVEL_PUBLIC | BsConfig::RENDER_AS_JAVASCRIPT | BsConfig::TYPE_INT, 'bs-pref-BSPingInterval');
		BsConfig::registerVar('MW::SortAlph', false, BsConfig::LEVEL_PUBLIC | BsConfig::LEVEL_USER | BsConfig::TYPE_BOOL, 'bs-pref-sortalph', 'toggle');

		$aRegisteredApplications = BsConfig::get('Core::Applications');
		$aRegisteredApplications[] = array(
			'name' => 'Wiki',
			'displaytitle' => 'Wiki',
			'url' => $this->get('Server') . $this->get('ScriptPath')
		);
		BsConfig::set('Core::Applications', $aRegisteredApplications);
		BsConfig::set('Core::ApplicationContext', 'Wiki');
		wfProfileOut('BS::' . __METHOD__);
	}

	public function set($key, $value) {
		wfProfileIn('BS::' . __METHOD__);
		$name = 'wg' . $key;
		global $$name;
		$$name = $value;
		wfProfileOut('BS::' . __METHOD__);
	}

	public function add($key, $value) {
		wfProfileIn('BS::' . __METHOD__);
		$name = 'wg' . $key;
		global $$name;
		if (is_object($$name)) {
			//@todo Fehlermeldung Typ unterstützt kein ADD
			wfProfileOut('BS::' . __METHOD__);
			return;
		}
		if (is_int($$name) || is_float($$name) || is_numeric($$name)) {
			$$name += $value;
		} elseif (is_string($$name)) {
			$$name .= $value;
		} elseif (is_bool($$name)) {
			if ($value) {
				$$name = $value;
			}
		} else {
			$$name = array_merge_recursive($$name, $value);
		}
		wfProfileOut('BS::' . __METHOD__);
		return $$name;
	}

	public function get($key) {
		wfProfileIn('BS::' . __METHOD__);
		$name = 'wg' . $key;
		global $$name;
		wfProfileOut('BS::' . __METHOD__);
		return $$name;
	}

	public function &__get($name) {
		wfProfileIn('BS::' . __METHOD__);
		$key = 'wg' . $name;

		global $$key;
		$this->$name = & $$key;
		wfProfileOut('BS::' . __METHOD__);
		return $this->$name;
	}

	/**
	 * This method get called from the mediawiki hook "MediaWikiPerformAction.
	 * So the BlueSpice framework get not started on load.php and api.php.
	 * Also this hook is just running after mediawiki has checked the request for
	 * redirects and special request cases like invalid titles and similar things.
	 * 
	 * @param OutputPage $oOutput
	 * @param Article $oArticle
	 * @param Title $oTitle
	 * @param User $oUser
	 * @param Request $oRequest
	 * @param MediaWiki $oWiki
	 * @return bool
	 */
	public function doSetup($oOutput, $oArticle, $oTitle, $oUser, $oRequest, $oWiki) {
		$oUser->load();

		BsConfig::loadUserSettings($oUser);

		$bRunLegacyMode = false;

		if (method_exists($oWiki, 'getVal')) {
			$bRunLegacyMode = true;
			$sAction = $oWiki->getVal('Action');
		} else {
			$sAction = $oWiki->getAction();
		}
		// Workaround for mediawiki bug #20966: inability of IE to provide an action dependent
		// on which submit button is clicked.
		if ($sAction === 'historysubmit') {
			if ($oRequest->getBool('revisiondelete')) {
				$sAction = 'revisiondelete';
			} else {
				$sAction = 'view';
			}
		}

		switch ($sAction) {
			case 'remote':
				$this->mRunlevel = BsRUNLEVEL::REMOTE;
				BsExtensionManager::loadExtensions(BsRUNLEVEL::REMOTE, $this);
				if ($bRunLegacyMode) {
					$this->doProcessRemoteAction($oOutput);
					return false;
				}
				break;
			default:
				$this->mRunlevel = BsRUNLEVEL::FULL;
				BsOutputHandler::init();
				BsExtensionManager::loadExtensions(BsRUNLEVEL::FULL, $this);
				break;
		}

		return true;
	}

	/**
	 * additional chances to reject an uploaded file
	 * @param string $saveName: destination file name
	 * @param string $tempName: filesystem path to the temporary file for checks
	 * @param string &$error: output: message key for message to show if upload canceled by returning false. May also be an array, where the first element
										is the message key and the remaining elements are used as parameters to the message.
	 * @return bool true on success , false on failure
	 */
	public function onUploadVerification( $saveName, $tempName, &$error ) {
		$aParts = explode( '.', $saveName );
		if ( !empty( $aParts[0] ) ) {
			$oUser = User::newFromName( $aParts[0] );
			if ( $oUser->getId() != 0 ) {
				global $wgUser;
				if ( strcasecmp( $oUser->getName(), $wgUser->getName() ) != 0 ) {
					$error = 'bs-imageofotheruser';
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * This method fetches the output of all remote actions and give it back to the calling method,
	 * which depends on the running mediawiki version.
	 */
	public function getRemoteActionContent($oOutput) {
		$sOutput = '';

		$sModule = BsCore::getParam('mod');
		$sMethod = BsCore::getParam('rf');

		if (!isset($this->RemoteHandler[$sModule][$sMethod])) {
			$sOutput = json_encode( array(
				'success' => false,
				'status'  => 'error',
				'message' => wfMessage('bs-remotehandler-method-not-found', $sModule, $sMethod)->plain()
			) );
			return $sOutput;
		}
		$oInstance = $this->RemoteHandler[$sModule][$sMethod]['instance'];

		// "*" overrides permission check for SecureFileStore
		if ($this->RemoteHandler[$sModule][$sMethod]['perms'] != "*") {
			if (!BsAdapterMW::checkAccessAdmission($this->RemoteHandler[$sModule][$sMethod]['perms'])) {
				$sOutput = json_encode( array(
					'success' => false,
					'status'  => 'error',
					'message' =>  wfMessage('bs-remotehandler-insufficient-permissions')->plain()
				) );
				return $sOutput;
			}
		}

		if ($oInstance != null && is_callable(array($oInstance, $sMethod))) {
			$oInstance->$sMethod($sOutput);
		}
		else {
			$sOutput = json_encode( array(
				'success' => false,
				'status'  => 'error',
				'message' => wfMessage('bs-remotehandler-method-not-callable', $sModule, $sMethod)->plain()
			) );
		}

		return $sOutput;
	}

	/**
	 * This method belongs to the new doSetup-Method and replaces onUnknownAction.
	 * It gets called if the mediawiki action is "remote" and handles the output
	 * with the OutputPage class, which is more better than just to kill the
	 * request process to prevent unnecessary output.
	 * 
	 * @param OutputPage $oOutput
	 */
	protected function doProcessRemoteAction($oOutput) {
		$sOutput = $this->getRemoteActionContent($oOutput);
		if ($sOutput) {
			$oOutput->setArticleBodyOnly(true);
			$oOutput->addHTML($sOutput);
		}
	}

	public function doInitialise() {
		$sAdapterPath = BsConfig::get('CORE::BlueSpiceAdapterPath');
		BsValidator::registerPluginPath($sAdapterPath . DS . 'plugins' . DS . 'BsValidator');
		BsExtensionManager::includeExtentionFiles($this);
	}

	protected $bUserFetchRights = false;

	/**
	 * 
	 * @param User $oUser
	 * @param array $aRights
	 * @return boolean
	 */
	public function onUserGetRights($oUser, &$aRights) {
		wfProfileIn('BS::' . __METHOD__);
		if($oUser->isAnon()) {
			$iUserId = BsCore::getParam('u', '', BsPARAM::GET | BsPARAMTYPE::STRING);
			$sUserHash = BsCore::getParam('h', false, BsPARAM::GET | BsPARAMTYPE::STRING);
			if (!empty($iUserId) && !empty($sUserHash)) {
				$this->loggedInByHash = true;
				$_user = User::newFromName($iUserId);
				if ($_user !== false && $sUserHash == $_user->getToken()) {
					$result = $_user->isAllowed('read');
					$oUser = $_user;
				}
			}
		}
		if ($this->bUserFetchRights == false) {
			$aRights = User::getGroupPermissions($oUser->getEffectiveGroups(true));
			# The flag is deactivated to prevent some bugs with the loading of the actual users rights.
			# $this->bUserFetchRights = true;
		}
		wfProfileOut('BS::' . __METHOD__);
		return true;
	}

	/**
	 * This function triggers User::isAllowed when userCanRead is called. This 
	 * leads to an early initialization of $user object, which is needed in 
	 * order to have correct permission sets in BlueSpice.
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @param boolean $result
	 */
	public function onUserCan(&$title, &$user, $action, &$result) {
		//wfProfileIn('BS::' . __METHOD__);
		if (!$this->loggedInByHash) {
			wfProfileIn('--BS::' . __METHOD__ . 'if !$this->loggedInByHash');
			$iUserId = BsCore::getParam('u', '', BsPARAM::GET | BsPARAMTYPE::STRING);
			$sUserHash = BsCore::getParam('h', false, BsPARAM::GET | BsPARAMTYPE::STRING);
			if (empty($iUserId) || empty($sUserHash)) {
				wfProfileOut('--BS::' . __METHOD__ . 'if !$this->loggedInByHash');
				return true;
			}

			$user->mGroups = array();
			$user->getEffectiveGroups(true);
			if ($iUserId && $sUserHash) {
				$this->loggedInByHash = true;
				$_user = User::newFromName($iUserId);
				if ($_user !== false && $sUserHash == $_user->getToken()) {
					$result = $_user->isAllowed('read');
					$user = $_user;
				}
			}
			wfProfileOut('--BS::' . __METHOD__ . 'if !$this->loggedInByHash');
		}

		if ($action == 'read') {
			$result = $user->isAllowed($action);
		}
		//wfProfileOut('BS::' . __METHOD__);
		return true;
	}

	public function getVarName($name, $global = false) {
		if ($global || isset($GLOBALS['wg' . $name])) {
			return 'wg' . $name;
		}
		return $name;
	}

	protected $RemoteHandler = array();

	public function addRemoteHandler($_sName, &$_oInstance, $_sMethodName, $_sPermissionNeeded) {
		wfProfileIn('BS::' . __METHOD__);
		$this->RemoteHandler[$_sName][$_sMethodName] = array('instance' => $_oInstance, 'perms' => $_sPermissionNeeded);
		wfProfileOut('BS::' . __METHOD__);
	}

	public function onUnknownAction($action, $article) {
		// TODO SW(05.01.12 15:34): Profiling
		if ($action != 'remote') {
			return true;
		} else {
			var_dump($article);
			return false;
		}
		if ($this->mRunlevel == BsRUNLEVEL::REMOTE) {
			$sOutput = '';

			$sModule = BsCore::getParam('mod');
			$sMethod = BsCore::getParam('rf');

			if (!isset($this->RemoteHandler[$sModule][$sMethod])) {
				echo "Falsche Methode"; // @todo Errorhandler
				exit();
				//return true;
			}
			$oInstance = $this->RemoteHandler[$sModule][$sMethod]['instance'];

			// "*" overrides permission check for SecureFileStore
			if ($this->RemoteHandler[$sModule][$sMethod]['perms'] != "*") {
				if (!BsAdapterMW::checkAccessAdmission($this->RemoteHandler[$sModule][$sMethod]['perms'])) {
					echo "Falsche Rechte"; // @todo Errorhandler
					exit();
					//return true;
				}
			}

			// todo: do not use method_exists, use is_callable instead
			// http://abcphp.com/out/method-exists-vs-is-callable/
			if ($oInstance != null && method_exists($oInstance, $sMethod)) {
				$oInstance->$sMethod($sOutput);
			}

			// TODO MRG (15.11.10 14:58): der autocommit geht nicht immer, das sollte man zumindest optional machen.
			$dbw = wfGetDB(DB_MASTER);
			$dbw->commit();

			echo $sOutput;
			// TODO MRG (15.11.10 14:58): warum nicht exit?
			die();
		}
		return true;
	}

	// todo msc 2011-04-27 wiederholter Aufruf führt schnell zu einem Speicherüberlauf (>128MB bei Indexierung)
	// scheinbar wird ausserhalb der Methode gecacht! Aufruf mit adapter->parseWikiText($text, true) schafft KEINE Abhilfe.
	public function parseWikiText($text, $nocache = false, $numberheadings = null) {
		wfProfileIn('BS::' . __METHOD__);

		if ( !$this->oLocalParser )
			$this->oLocalParser = new Parser(); // msc 2011-04-27 vorschlag: als member-Objekt im Adapter anlegen

		$parserOptions = new ParserOptions();

		if ( $numberheadings === false )
			$parserOptions->setNumberHeadings( false );
		else if ( $numberheadings === true )
			$parserOptions->setNumberHeadings( true );

		// TODO MRG20110707: Check it this cannot be unified
		global $wgVersion;
		if ( version_compare( $wgVersion, '1.17.0', '>' ) ) {
			if ( $nocache )
				$this->oLocalParser->disableCache(); // TODO RBV (19.10.10 15:57): --> Strict Standards: Creating default object from empty value in ...\includes\parser\Parser.php  on line 4433
		}

		if ( !( $this->Title instanceof Title ) ) {
			return '';
		}

		$output = $this->oLocalParser->parse(
				$text, $this->Title, // todo msc 2011-04-27 welches Title-Objekt wird hier verwendet? Kann das parametriert werden?
				$parserOptions, true
		);

		wfProfileOut('BS::' . __METHOD__);
		return $output->getText();
	}

	protected function initializeDatabase() {
		wfProfileIn('BS::' . __METHOD__);
		$options = array();
		$options['host'] = $this->DBserver;
		$options['user'] = $this->DBuser;
		$options['pass'] = $this->DBpassword;
		$options['dbname'] = $this->DBname;
		$options['port'] = $this->DBport;
		$options['prefix'] = $this->DBprefix; // $wgDBprefix
		BsDatabase::getInstance('MW', $options);
		wfProfileOut('BS::' . __METHOD__);
	}

	public function onEditPageShowEditFormInitial(&$editForm) {
		// TODO SW(05.01.12 15:36): Profiling
		$buttonranking = $this->aEditButtonRanking;
		if (!$buttonranking || !is_array($buttonranking))
			return true;

		$oEditButtonPaneView = new ViewEditButtonPane();
		foreach ($buttonranking as $name) {
			if (!isset($this->aEditButtons[$name])) {
				continue;
			}
			$oEditButtonView = new ViewEditButton();
			$oEditButtonView->setId($this->aEditButtons[$name]['id']);
			$oEditButtonView->setOnClick($this->aEditButtons[$name]['onclick']);
			$oEditButtonView->setImage($this->aEditButtons[$name]['image']);
			$oEditButtonView->setMsg($this->aEditButtons[$name]['msg']);

			$oEditButtonPaneView->addItem($oEditButtonView);
		}

		$editForm->editFormTextTop .= $oEditButtonPaneView->execute();
		return true;
	}

	public function addEditButton($name, $params, $priority = false) {
		if( $priority != false ) {
			if( isset( $this->aEditButtonRanking[$priority] ) ) {
				$this->aEditButtonRanking[] = $name;
			}
			else {
				$this->aEditButtonRanking[$priority] = $name;
			}
		}
		else {
			$this->aEditButtonRanking[] = $name;
		}
		$this->aEditButtons[$name] = $params;
	}

	public function removeEditButton($name) {
		if (isset($this->aEditButtons[$name]))
			unset($this->aEditButtons[$name]);
	}

	// TODO RBV (02.11.10 10:37): "Fatal error: Arrays are not allowed in class constants". Therefore encapsulation.
	public static function getForbiddenCharsInArticleTitle() {
		return BsCore::getForbiddenCharsInArticleTitle();
	}

	public function runPreferencePlugin($sAdapterName, $oVariable) {
		switch ($sAdapterName) {
			case 'MW':
				return $this->runPreferencePluginMW($oVariable);
				break;
			default:
				return array();
		}
	}

	protected function runPreferencePluginMW($oVariable) {
		switch ($oVariable->getName()) {
			case 'SkinWidgetDirection' :
				return array(
					'options' => array(
						'left' => 'left',
						'right' => 'right'
					)
				);
				break;
			default:
				return array();
		}
	}

	/**
	 * Registeres a permission with the MediaWiki Framework.
	 * object for proper internationalisation of your permission. Every
	 * permission is granted automatically to the user group 'sysop'. You can
	 * specify additional groups through the third parameter.
	 * @param String $sPermissionName I.e. 'myextension-dosomething'
	 * @param Array $aUserGroups User groups that get preinitialized with the new
	 * pemission. I.e. array( 'user', 'bureaucrats' )
	 * @return void
	 */
	// TODO MRG (05.02.11 19:24): @Sebastian Ist der dritte Parameter im PermissionsManager berücksichtigt?
	public function registerPermission($sPermissionName, $aUserGroups = array()) {
		wfProfileIn('BS::' . __METHOD__);

		global $wgGroupPermissions;
		$wgGroupPermissions['sysop'][$sPermissionName] = true;

		foreach ($aUserGroups as $sGroup) {
			// check if it is not set already
			if (!isset($wgGroupPermissions[$sGroup][$sPermissionName])) {
				$wgGroupPermissions[$sGroup][$sPermissionName] = true;
			}
		}

		wfProfileOut('BS::' . __METHOD__);
	}

	/**
	 * Registeres a SpecialPage with the MediaWiki Framework.
	 * @param String $sSpecialPageName
	 * @param String $sSpecialPagePath
	 * @param String $sAliasName
	 * @param String $sSpecialPageGroup
	 * @return void
	 */
	public function registerSpecialPage($sSpecialPageName, $sSpecialPagePath, $sAliasName, $sSpecialPageGroup = 'bluespice') {
		wfProfileIn('BS::' . __METHOD__);
		global $wgSpecialPages, $wgExtensionMessagesFiles, $wgSpecialPageGroups, $wgAutoloadClasses;

		$wgAutoloadClasses[$sSpecialPageName] = $sSpecialPagePath . '/' . $sSpecialPageName . '.class.php';
		$wgSpecialPages[$sSpecialPageName] = $sSpecialPageName;
		$wgSpecialPageGroups[$sSpecialPageName] = $sSpecialPageGroup;
		$wgExtensionMessagesFiles[$sAliasName] = $sSpecialPagePath . '/' . $sSpecialPageName . '.alias.php';
		wfProfileOut('BS::' . __METHOD__);
	}

	public function registerBehaviorSwitch($sMagicWord, $aCallback = null ) {
		if( is_callable($aCallback) ) {
			$this->aBehaviorSwitches[$sMagicWord] = $aCallback;
		}
		else if( !isset(MagicWord::$mDoubleUnderscoreIDs[$sMagicWord]) ) {
			MagicWord::$mDoubleUnderscoreIDs[] = $sMagicWord;
		}
	}

	// TODO MRG (01.12.10 00:07): Ich bezweifle, dass wir diese Funktion brauchen
	public function behaviorSwitches(&$article, &$content) {
		// TODO SW(05.01.12 15:37): Profiling
		if (!isset($this->aBehaviorSwitches))
			return true;

		$sNowikistripped = preg_replace("/<nowiki>.*?<\/nowiki>/i", "", $content);
		foreach ($this->aBehaviorSwitches as $sSwitch => $sCallback) {
			if (strstr($sNowikistripped, '__' . $sSwitch . '__')) {
				call_user_func($sCallback);
			}
		}
		return true;
	}

	public function hideBehaviorSwitches(&$parser, &$text) {
		// TODO SW(05.01.12 15:37): Profiling
		if (!isset($this->aBehaviorSwitches))
			return true;

		$sNowikistripped = preg_replace("/<nowiki>.*?<\/nowiki>/i", "", $text);
		foreach ($this->aBehaviorSwitches as $sSwitch => $sCallback) {
			if (strstr($sNowikistripped, '__' . $sSwitch . '__')) {
				call_user_func($sCallback);
			}
			// TODO MRG (01.12.10 00:08): Wahrscheinlich kann man das auch gleich beim ersten preg_replace machen
			$text = preg_replace("/(<nowiki>.*?)__{$sSwitch}__(.*?<\/nowiki>)/i", "$1@@{$sSwitch}@@$2", $text);
		}
		return true;
	}

	public function recoverBehaviorSwitches(&$parser, &$text) {
		// TODO SW(05.01.12 15:38): Profiling
		if (!isset($this->aBehaviorSwitches))
			return true;

		foreach ($this->aBehaviorSwitches as $sSwitch => $sCallback) {
			$text = str_replace('__' . $sSwitch . '__', "", $text);
			$text = preg_replace("/@@" . $sSwitch . "@@/", '__' . $sSwitch . '__', $text);
		}
		return true;
	}

	/**
	 * Hook-Handler for 'BSBlueSpiceSkinAfterArticleContent'. Creates a settings toolbox on the users own page.
	 * @param array $aViews Array of views to be rendered in skin
	 * @param User $oUser Current user object
	 * @param Title $oTitle Current title object
	 * @return bool Always true to keep hook running.
	 */
	public function onBlueSpiceSkinAfterArticleContent(&$aViews, $oUser, $oTitle) {
		if ( !$oTitle->equals( $oUser->getUserPage() ) )
			return true; //Run only if on current users profile/userpage
		wfProfileIn('BS::' . __METHOD__);
		$aSettingViews = array();
		wfRunHooks( 'BS:UserPageSettings', array( $oUser, $oTitle, &$aSettingViews ) );

		$oUserPageSettingsView = new ViewBaseElement();
		$oUserPageSettingsView->setAutoWrap('<div id="bs-usersidebar-settings" class="bs-userpagesettings-item">###CONTENT###</div>');
		$oUserPageSettingsView->setTemplate(
				'<a href="{URL}" title="{TITLE}"><img alt="{IMGALT}" src="{IMGSRC}" /><div class="bs-user-label">{TEXT}</div></a>'
		);
		$oUserPageSettingsView->addData(
				array(
					'URL' => htmlspecialchars( Title::newFromText('Special:Preferences')->getLinkURL() ),
					'TITLE' => wfMsg('bs-userpreferences-link-title'),
					'TEXT' => wfMsg('bs-userpreferences-link-text'),
					'IMGALT' => wfMsg('bs-userpreferences-link-title'),
					'IMGSRC' => $this->get('ScriptPath') . '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-userpage-settings.png',
				)
		);

		$aSettingViews[] = $oUserPageSettingsView;

		$oProfilePageSettingsView = new ViewBaseElement();
		$oProfilePageSettingsView->setId('bs-userpagesettings');

		$oProfilePageSettingsFieldsetView = new ViewFormElementFieldset();
		$oProfilePageSettingsFieldsetView->setLabel(
				wfMsg('bs-userpagesettings-legend')
		);

		foreach ($aSettingViews as $oSettingsView) {
			$oProfilePageSettingsFieldsetView->addItem($oSettingsView);
		}

		$oProfilePageSettingsView->addItem($oProfilePageSettingsFieldsetView);
		$aViews[] = $oProfilePageSettingsView;
		wfProfileOut('BS::' . __METHOD__);
		return true;
	}

	/**
	 * Needed for edit and sumbit (preview) mode
	 * @global <type> $wgArticle
	 * @param <type> $editPage
	 * @return <type> 
	 */
	public function lastChanceBehaviorSwitches($editPage) {
		// TODO SW(05.01.12 15:39): Profiling
		global $wgArticle;
		if (!isset($this->aBehaviorSwitches))
			return true;

		$sNowikistripped = preg_replace("/<nowiki>.*?<\/nowiki>/mi", "", $wgArticle->getContent());
		foreach ($this->aBehaviorSwitches as $sSwitch => $sCallback) {
			if (strstr($sNowikistripped, '__' . $sSwitch . '__')) {
				call_user_func($sCallback);
			}
		}
		// TODO: This note should be displayed when the editor is deactivated
		//$editPage->editFormTextTop = "Der Editor wurde deaktiviert <br/>";
		if (isset($editPage->textbox1)) {
			foreach ($this->aBehaviorSwitches as $sSwitch => $sCallback) {
				$sNowikistripped = preg_replace("/<nowiki>.*?<\/nowiki>/mi", "", $editPage->textbox1);
				if (strstr($sNowikistripped, '__' . $sSwitch . '__')) {
					call_user_func($sCallback);
				}
			}
		}
		return true;
	}

	/**
	 * Retrieves the categories a title is assigned to.
	 * @param Title $oTitle The requested MediwWiki Title object
	 * @return Array An array containing the _un_prefixed category titles
	 */
	public static function getCategoriesForTitle($oTitle) {
		wfProfileIn('BS::' . __METHOD__);
		/* Title::getParentCategories() returns an array like this:
		 * array (
		 *  'Category:Foo' => 'My Article',
		 *  'Category:Bar' => 'My Article',
		 *  'Category:Baz' => 'My Article',
		 * )
		 */
		$aCategories = $oTitle->getParentCategories();
		$aSimpleCategoryList = array();
		if (!empty($aCategories)) {
			foreach ($aCategories as $sCategoryPageName => $sCurrentTitle) {
				$aCategoryPageNameParts = explode(':', $sCategoryPageName);
				$aSimpleCategoryList[] = $aCategoryPageNameParts[1];
			}
		}
		wfProfileOut('BS::' . __METHOD__);
		return $aSimpleCategoryList;
	}

	/**
	 * Simple caching mechanism for UserMiniProfiles
	 * @var array 
	 */
	protected static $aUserMiniProfiles = array();

	/**
	 * Creates a miniprofile for a user. It consists if the useres profile image
	 * and links to his userpage. In future versions it should also have a
	 * little menu with his mail adress, and other profile information.
	 * @param User $oUser The requested MediaWiki User object
	 * @param array $aParams The settings array for the mini profile view object
	 * @return ViewUserMiniProfile A view with the users mini profile
	 */
	public static function getUserMiniProfile( $oUser, $aParams = array() ) {
		wfProfileIn('BS::' . __METHOD__);
		$sParamsHash = md5(serialize($aParams));
		$sViewHash = $oUser->getName() . $sParamsHash;
		if (isset(self::$aUserMiniProfiles[$sViewHash])) {
			wfProfileOut('BS::' . __METHOD__);
			return self::$aUserMiniProfiles[$sViewHash];
		}
		$oUserMiniProfileView = new ViewUserMiniProfile();
		$oUserMiniProfileView->setOptions($aParams);
		$oUserMiniProfileView->setOption('user', $oUser);
		wfRunHooks( 'BSAdapterGetUserMiniProfileBeforeInit', array( $oUserMiniProfileView, $oUser, $aParams ) );
		$oUserMiniProfileView->init();

		self::$aUserMiniProfiles[$sViewHash] = $oUserMiniProfileView;
		wfProfileOut('BS::' . __METHOD__);
		return $oUserMiniProfileView;
	}
	
	/**
	 * Make the page being parsed have a dependency on $page via the templatelinks table. 
	 * http://www.mediawiki.org/wiki/Manual:Tag_extensions#Regenerating_the_page_when_another_page_is_edited
	 * @param Parser $oParser
	 * @param String $sTitle
	 */
	public static function addTemplateLinkDependencyByText($oParser, $sTitle) {
		$oTitle = Title::newFromText( $sTitle );
		static::addTemplateLinkDependency($oParser, $oTitle);
	}

	/**
	 * Make the page being parsed have a dependency on $page via the templatelinks table. 
	 * http://www.mediawiki.org/wiki/Manual:Tag_extensions#Regenerating_the_page_when_another_page_is_edited
	 * @param Parser $oParser
	 * @param Title $oTitle
	 */
	public static function addTemplateLinkDependency( $oParser, $oTitle )  {
		$oRevision = Revision::newFromTitle( $oTitle );
		$iPageId = $oRevision ? $oRevision->getPage() : 0;
		$iRevId  = $oRevision ? $oRevision->getId()   : 0;
		
		$oParser->getOutput()->addTemplate( 
			$oTitle,
			$iPageId,
			$iRevId
		); // Register dependency in templatelinks
	}

	/**
	 * Adds the default values for the searchbox.
	 * @param Object $callingInstance. Object of the calling Instance
	 * @param Array $aSearchBoxKeyValues. A reference of the form value array.
	 * @return bool Always true to keep hook running.
	 */
	public function onFormDefaults( $callingInstance, &$aSearchBoxKeyValues ) {
		wfProfileIn('BS::' . __METHOD__);
		$aLocalUrl = explode('?', SpecialPage::getTitleFor('Search')->getLocalUrl());

		$aSearchBoxKeyValues['SubmitButtonTitle'] = wfMsg('bs-extended-search-tooltip-title', 'Search for titles');
		$aSearchBoxKeyValues['SubmitButtonFulltext'] = wfMsg('bs-extended-search-tooltip-fulltext', 'Search inside articles');
		$aSearchBoxKeyValues['SearchTextFieldTitle'] = wfMsg('bs-extended-search-textfield-tooltip', 'Search BlueSpice for Mediawiki [alt-shift-f]');
		$aSearchBoxKeyValues['SearchTextFieldDefaultText'] = wfMsg('bs-extended-search-textfield-defaultvalue', 'Search...');

		if (isset($aSearchBoxKeyValues['SearchDestination']))
			return true;

		//$searchBoxKeyValues['SearchDestination'] = SpecialPage::getTitleFor('SpecialExtendedSearch')->getLocalUrl(); //BsConfig::get( 'MW::ScriptPath' ).'/index.php/Special:SpecialExtendedSearch';
		$aSearchBoxKeyValues['SearchDestination'] = $aLocalUrl[0];
		if (isset($aLocalUrl[1]) && strpos($aLocalUrl[1], '=') !== false) {
			$aTitle = explode('=', $aLocalUrl[1]);
			$aSearchBoxKeyValues['HiddenFields']['title'] = $aTitle[1];
		}
		$aSearchBoxKeyValues['SearchTextFieldName'] = 'search';
		$aSearchBoxKeyValues['DefaultKeyValuePair'] = array('button', '');
		$aSearchBoxKeyValues['TitleKeyValuePair'] = array('button', '');
		$aSearchBoxKeyValues['FulltextKeyValuePair'] = array('fulltext', 'Search');
		$aSearchBoxKeyValues['method'] = 'post'; // mediawiki's default
		wfProfileOut('BS::' . __METHOD__);
		return true;
	}

	/**
	 * @global String $wgVersion
	 * @global Array $wgGroupPermissions
	 * @param User $oUser
	 * @param String $sGroupName
	 * @param Array $aPermissions
	 * @return boolean alway true - keeps the hook system running
	 */
	private $sTempGroup = '';
	public function addTemporaryGroupToUser( $oUser, $sGroupName, $aPermissions ) {
		global $wgVersion, $wgGroupPermissions;
		
		foreach( $aPermissions as $sPermission ) {
			$wgGroupPermissions[$sGroupName][$sPermission]	= true;
		}
		
		if($wgVersion > '1.17') $this->sTempGroup = $sGroupName;
		
		$oUser->addGroup($sGroupName);
		
		if($wgVersion > '1.17') return true;
		
		$dbw = wfGetDB( DB_MASTER );
		$dbw->delete( 'user_groups',
			array(
				'ug_user'  => $oUser->getID(),
				'ug_group' => $sGroupName,
			) 
		);
		
		return true;
	}
	
	/**
	 * Hook-Handler for MediaWiki hook UserAddGroup
	 * @param User $user
	 * @param String $group
	 * @return boolean - returns false to skip saving group into db
	 */
	public function addTemporaryGroupToUserHelper($user, &$group) {
		if( empty($this->sTempGroup) || $this->sTempGroup !== $group ) return true;
		$this->sTempGroup = '';
		return false;
	}

	/**
	 * Returns redirect target title or null if there is no redirect
	 * @global string $wgVersion
	 * @param Title $oTitle
	 * @return Title - returns redirect target title or null
	 */
	public function getTitleFromRedirectRecurse( $oTitle ) {
		global $wgVersion;
		//PW(26.04.2013) TODO: re-visit on later version
		if( version_compare($wgVersion, '1.19.0', '<') ) {
			$oArticle = new Article( $oTitle, 0 ); //New: current revision
			$sContent = $oArticle->fetchContent( 0 ); //Old: current revision
			return Title::newFromRedirectRecurse( $sContent );
		}

		$oWikiPage = WikiPage::newFromID( $oTitle->getArticleID() );
		return $oWikiPage->getRedirectTarget();
	}

	public static function ajaxBSPing() {
		$aResult = array(
			"success" => false,
			"errors" => array(),
			"message" => '',
		);
		$iArticleId  = BsCore::getParam( 'iArticleID', 0,       BsPARAMTYPE::INT        |BsPARAM::POST );
		$iNamespace  = BsCore::getParam( 'iNamespace', 0,       BsPARAMTYPE::INT        |BsPARAM::POST );
		$sTitle      = BsCore::getParam( 'sTitle',     '',      BsPARAMTYPE::STRING     |BsPARAM::POST );
		$iRevision   = BsCore::getParam( 'iRevision',  0,       BsPARAMTYPE::INT        |BsPARAM::POST );
		$aBSPingData = BsCore::getParam( 'BsPingData', array(), BsPARAMTYPE::ARRAY_MIXED|BsPARAM::POST );

		$aResult['success'] = true;
		foreach($aBSPingData as $aSinglePing) {
			if( empty($aSinglePing['sRef']) ) continue;
			if( !$aResult['success'] ) break;
			
			if( !isset($aSinglePing['aData']) )
				$aSinglePing['aData'] = array();

			$aSingleResult = array(
				"success" => false,
				"errors" => array(),
				"message" => '',
			);
			//if hook returns false - overall success is false
			$aResult['success'] = wfRunHooks('BsAdapterAjaxPingResult', array( $aSinglePing['sRef'], $aSinglePing['aData'], $iArticleId, $sTitle, $iNamespace, $iRevision, &$aSingleResult ));
			$aResult[$aSinglePing['sRef']] = $aSingleResult;
		}

		return json_encode($aResult);
	}
}