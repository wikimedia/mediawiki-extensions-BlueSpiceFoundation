<?php

/**
 * This file contains the BsCore class.
 *
 * The BsCore class is the main class of the BlueSpice framework.
 * It controlls the whole life sequence of the framework.
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
 * @author     Stephan Muggli <muggli@hallowelt.biz>
 * @version    0.1.0
 * @package    Bluespice_Core
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * the BsCore
 * @package BlueSpice_Core
 * @subpackage Core
 */
class BsCore {

	public $aBehaviorSwitches = array();

	protected $aEditButtons = array();

	protected $aEditButtonRanking = array();

	/**
	 * Array of illegal chars in article title
	 * @var array
	 */
	protected static $prForbiddenCharsInArticleTitle = array( '#', '<', '>', '[', ']', '|', '{', '}' );
	/**
	 * an array of adapter instances
	 * @var array
	 */
	protected static $oInstance = null;
	/**
	 * a state flag if ExtJs is already loaded
	 * @var bool
	 */
	protected static $bExtJsLoaded = false;
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
	 * Local Parser
	 * @var object
	 */
	protected static $oLocalParser = false;
	/**
	 * Local Parser
	 * @var object
	 */
	protected static $oLocalParserOptions = false;
	/**
	 * Current User Object
	 * @var object
	 */
	protected static $prCurrentUser = null;
	/**
	 * Simple caching mechanism for UserMiniProfiles
	 * @var array
	 */
	protected static $aUserMiniProfiles = array();

	protected static $aClientScriptBlocks = array();

	protected static $bHtmlFormClassLoaded = false;

	/**
	 * The constructor is protected because of the singleton pattern.
	 */
	protected function __construct() {
		wfProfileIn('Performance: ' . __METHOD__);

		global $wgStylePath, $wgScriptPath, $wgServer, $oMobileDetect;
		$sStylePath = ( $wgStylePath ? $wgStylePath : $wgScriptPath ) . "/BlueSpiceSkin/resources/images/";
		$sStylePath .= is_object( $oMobileDetect ) && $oMobileDetect->isMobile() ? "mobile" : "desktop";

		$aRegisteredApplications[] = array(
			'name' => 'Wiki',
			'displaytitle' => 'Wiki',
			'url' => $wgServer . $wgScriptPath
		);

		BsConfig::registerVar( 'MW::CanonicalNamespaceNames', array(), BsConfig::LEVEL_ADAPTER );
		BsConfig::registerVar( 'MW::LanguageNames', array(), BsConfig::LEVEL_ADAPTER );
		BsConfig::registerVar( 'MW::ScriptPath', $wgScriptPath, BsConfig::LEVEL_ADAPTER );
		BsConfig::registerVar( 'MW::FileExtensions', array('doc', 'docx', 'pdf', 'xls'), BsConfig::LEVEL_PUBLIC  | BsConfig::TYPE_ARRAY_STRING, 'bs-pref-FileExtensions', 'multiselectplusadd' );
		BsConfig::registerVar( 'MW::ImageExtensions', array('png', 'gif', 'jpg', 'jpeg'), BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_ARRAY_STRING, 'bs-pref-ImageExtensions', 'multiselectplusadd' );
		BsConfig::registerVar( 'MW::LogoPath', $sStylePath . '/bs-logo.png', BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_STRING, 'bs-pref-LogoPath' );
		BsConfig::registerVar( 'MW::FaviconPath', $sStylePath . '/favicon.ico', BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_STRING, 'bs-pref-FaviconPath' );
		BsConfig::registerVar( 'MW::DefaultUserImage', $sStylePath . '/bs-user-default-image.png', BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_STRING, 'bs-pref-DefaultUserImage' );
		BsConfig::registerVar( 'MW::MiniProfileEnforceHeight', true, BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_BOOL, 'bs-pref-MiniProfileEnforceHeight', 'toggle' );
		BsConfig::registerVar( 'MW::AnonUserImage', $sStylePath . '/bs-user-anon-image.png', BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_STRING, 'bs-pref-AnonUserImage' );
		BsConfig::registerVar( 'MW::DeletedUserImage', $sStylePath . '/bs-user-deleted-image.png', BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_STRING, 'bs-pref-DeletedUserImage' );
		BsConfig::registerVar( 'MW::RekursionBreakLevel', 20, BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_INT, 'bs-pref-RekursionBreakLevel' );
		BsConfig::registerVar( 'MW::UserImage', '', BsConfig::LEVEL_USER | BsConfig::TYPE_STRING | BsConfig::NO_DEFAULT, 'bs-authors-pref-UserImage' );
		BsConfig::registerVar( 'MW::PingInterval', 2, BsConfig::LEVEL_PUBLIC | BsConfig::RENDER_AS_JAVASCRIPT | BsConfig::TYPE_INT, 'bs-pref-BSPingInterval' );
		BsConfig::registerVar( 'MW::SortAlph', false, BsConfig::LEVEL_PUBLIC | BsConfig::LEVEL_USER | BsConfig::TYPE_BOOL, 'bs-pref-sortalph', 'toggle' );
		BsConfig::registerVar( 'MW::Applications', $aRegisteredApplications, BsConfig::LEVEL_PRIVATE | BsConfig::TYPE_ARRAY_MIXED, 'bs-Applications' );
		BsConfig::registerVar( 'MW::ApplicationContext', '', BsConfig::LEVEL_PRIVATE | BsConfig::TYPE_STRING | BsConfig::RENDER_AS_JAVASCRIPT, 'bs-pref-ApplicationContext' );
		BsConfig::registerVar( 'MW::TestMode', false, BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_BOOL, 'bs-pref-TestMode', 'toggle' );

		BsConfig::set( 'MW::ApplicationContext', 'Wiki' );
		wfProfileOut('Performance: ' . __METHOD__);
	}

	/**
	 * Tells BsScriptManager to load ExtJS
	 *
	 * If a request param 'debugExtJs' is set, the ExtJs debug file will be loaded.
	 * This method also loads the language file if needed.
	 * @deprecated since version 1.22+ This is now handeled in index.php
	 */
	public static function loadExtJs() {
		return;
	}

	public static function getForbiddenCharsInArticleTitle() {
		return self::$prForbiddenCharsInArticleTitle;
	}

	/**
	 * Used to access the singleton BlueSpice object.
	 * @return BsCore Singleton instance of BlueSpice object.
	 */
	public static function getInstance() {
		wfProfileIn('Performance: ' . __METHOD__);
		if ( self::$oInstance === null ) {
			self::$oInstance = new BsCore();
		}
		wfProfileOut('Performance: ' . __METHOD__);
		return self::$oInstance;
	}

	/**
	 * When retrieving data from sources different from the BsCore::getParam()
	 * method, use this interface to sanitize the value. For security reasons it
	 * is strongly recommended to use this method!
	 * @param mixed $handover The value that has to be sanitized.
	 * @param mixed $default A default value that gets returned, if the
	 * submitted value is not valid or does not match the requested BsPARAMTYPE.
	 * @param BsPARAMTYPE $options Sets the type of the expected return value.
	 * This information is used for proper sanitizing.
	 * @return mixed Depending on the BsPARAMTYPE sumbitted in $options the
	 * sanitized $handover or in case of invalidity of $handover, the $default
	 * value.
	 */
	public static function sanitize($handover, $default = false, $options = BsPARAMTYPE::STRING) {
		// TODO MRG20100725: Ist die Reihenfolge hier Ã¼berlegt? Was ist, wenn ich BsPARAMTYPE::INT & BsPARAMTYPE::STRING angebe?
		// TODO MRG20100725: Kann man das nicht mit getParam zusammenschalten, so dass diese Funktion sanitize verwendet?
		// TODO MRG20100725: Sollte $default nicht auch durch den sanitizer?
		/* Lilu:
		 * Die Reihenfolge ist meiner Meinung nach unerheblich, da immer nur der erste BsPARAMTYPE, der einen Treffer landet,
		 * zurÃ¼ckgegeben wird.
		 * Eine Trennung zwischen getParam und sanitize besteht, da man bei getParam angeben kann, ob man im Fehlerfall
		 * Default-Werte verwenden mÃ¶chte oder versucht werden soll, die Daten mit sanitize zu bereinigen.
		 * Ich denke, das jeder Programmierer seinen Extensions passende Default-Werte liefern sollte.
		 * Beim Sanitizen der Default-Werte entsteht sonst ggf. das Problem, das wir keinen gÃ¼ltigen Wert zurÃ¼ckgeben kÃ¶nnen. (null?)
		 * Das wÃ¼rde einen groÃŸen Vorteil des Sanitizers (die nicht mehr benÃ¶tigte GÃ¼ltigkeitsprÃ¼fung) wieder aushebeln.
		 */
		if ( $options & BsPARAMTYPE::RAW ) {
			return $handover;
		}
		if ( $options & BsPARAMTYPE::ARRAY_MIXED ) {
			if ( is_array( $handover ) ) {
				return $handover;
			}
			return array( $handover );
		}
		if ( $options & BsPARAMTYPE::NUMERIC ) {
			if ( is_numeric( $handover ) ) {
				return $handover;
			}
			return floatval( $handover );
		}
		if ( $options & BsPARAMTYPE::INT ) {
			if ( is_int( $handover ) ) {
				return $handover;
			}
			return intval( $handover );
		}
		if ( $options & BsPARAMTYPE::FLOAT ) {
			if ( is_float( $handover ) ) {
				return $handover;
			}
			return floatval( $handover );
		}
		if ( $options & BsPARAMTYPE::BOOL ) {
			if ( $handover == 'false' || $handover == '0' || $handover == '' ) {
				$handover = false;
			}
			if ( $handover == 'true' || $handover == '1' ) {
				$handover = true;
			}
			if ( is_bool( $handover ) ) {
				return $handover;
			}
			return (bool)$handover;
		}
		if ( $options & BsPARAMTYPE::STRING ) {
			if ( is_string( $handover ) ) {
				if ( $options & BsPARAMOPTION::CLEANUP_STRING ) {
					return addslashes( strip_tags( $handover ) );
				}
				return $handover;
			}
		}
		if ( $options & BsPARAMTYPE::SQL_STRING ) {
			if ( is_string( $handover ) ) {
				$oDb = wfGetDB( DB_SLAVE );
				// Use database specific escape methods
				$handover = $oDb->strencode( $handover );

				return $handover;
			}
		}
		if ( $options & BsPARAMTYPE::ARRAY_NUMERIC && is_array( $handover ) ) {
			foreach ( $handover as $k => $v ) {
				if ( !is_numeric( $v ) ) {
					$handover[$k] = null;
				}
			}
			return $handover;
		}
		if ( $options & BsPARAMTYPE::ARRAY_INT && is_array( $handover ) ) {
			foreach ( $handover as $key => $v ) {
				if ( !is_int( $v ) ) {
					$handover[$key] = null;
				}
			}
			return $handover;
		}
		if ( $options & BsPARAMTYPE::ARRAY_FLOAT && is_array( $handover ) ) {
			foreach ( $handover as $key => $v ) {
				if ( !is_float( $v ) ) {
					$handover[$key] = null;
				}
			}
			return $handover;
		}
		if ( $options & BsPARAMTYPE::ARRAY_BOOL && is_array( $handover ) ) {
			foreach ($handover as $key => $v) {
				if (!is_bool($v)) {
					$handover[$key] = null;
				}
			}
			return $handover;
		}
		if ( $options & BsPARAMTYPE::ARRAY_STRING && is_array( $handover ) ) {
			foreach ( $handover as $key => $v ) {
				if ( !is_string( $v ) ) {
					$handover[$key] = null;
				}
			}
			return $handover;
		}
		// TODO MRG20100725: Ich halte eine Option TRIM / TRIMRIGHT / TRIMLEFT fÃ¼r sinnvoll.
		// TODO MRG20100725: Ebenso HTMLENTITIES etc, wie unten beschrieben.
		return $default;
		/*
		 * Development Notes:
		 * further functions to think about:
		 * - htmlentieties() um die HTML Eingaben abzufangen
		 *    => html_entity_decode() um die Umwandlung rÃ¼ckgÃ¤ngig zu machen
		 * - htmlspecialchars() - Wandelt Sonderzeichen in HTML-Codes um
		 * ==> neither htmlentities() nor htmlspecialchars() are used in directory bluespice-mw or beyond (exc. GeSHi)
		 * - escapeshellcmd()
		 * - escapeshellarg()
		 * ==> Only used in Rss/extlib/Snoopy.class.inc and GeSHi
		 *
		 * Alternate options
		 * HTML Purifier : http://htmlpurifier.org/
		 * Popoon: http://svn.bitflux.ch/repos/public/popoon/trunk/classes/externalinput.php
		 */
	}

	/**
	 * When retrieving data from sources different from the BsCore::getParam()
	 * method, use this interface to sanitize an array. For security reasons it
	 * is strongly recommended to use this method!
	 * @param array $array The array that has to be sanitized.
	 * @param array $default A default array that gets returned, if the
	 * submitted array is not valid or does not match the requested BsPARAMTYPE.
	 * @param BsPARAMTYPE $options Sets the type of the expected return value.
	 * This information is used for proper sanitizing.
	 * @return array Depending on the BsPARAMTYPE sumbitted in $options the
	 * sanitized $array or in case of invalidity of $array, the $default
	 * array.
	 */
	public static function sanitizeArrayEntry($array, $key, $default = null, $options = null) {
		// TODO MRG20100725: Sollte $default nicht auch durch den sanitizer?
		if (!is_array($array)) {
			return $default;
		}
		if (!isset($array[$key])) {
			return $default;
		}
		return self::sanitize($array[$key], $default, $options);
	}

	/**
	 * Build an json-object for Ext.tree with the given nodes.
	 * @param array $nodes
	 * @return string
	 * @deprecated since version 1.22 Use ExtJSHelper class instead
	 */
	public static function buildTree($nodes) {
		wfProfileIn('Performance: ' . __METHOD__);
		$sTreeJSON = BsExtJSHelper::buildTree($nodes);
		wfProfileOut('Performance: ' . __METHOD__);
		return $sTreeJSON;
	}

	public static function doInitialise() {
		wfProfileIn('Performance: ' . __METHOD__ . ' - Initialize Core');
		self::$oInstance = new BsCore();
		wfProfileIn('Performance: ' . __METHOD__ . ' - Initialize Core');

		wfProfileIn('Performance: ' . __METHOD__ . ' - Load Settings');
		if ( !defined( 'DO_MAINTENANCE' ) ) {
			BsConfig::loadSettings();
		}
		wfProfileOut('Performance: ' . __METHOD__ . ' - Load Settings');

		wfProfileIn('Performance: ' . __METHOD__ . ' - Load and initialize all Extensions');
		BsExtensionManager::includeExtensionFiles( self::$oInstance );
		wfProfileOut('Performance: ' . __METHOD__ . ' - Load and initialize all Extensions');

		global $wgHooks;
		$wgHooks['ArticleAfterFetchContent'][] = array( self::$oInstance, 'behaviorSwitches' );
		$wgHooks['ParserBeforeStrip'][] = array( self::$oInstance, 'hideBehaviorSwitches' );
		$wgHooks['ParserBeforeTidy'][] = array( self::$oInstance, 'recoverBehaviorSwitches' );

		array_unshift(
			$wgHooks['EditPage::showEditForm:initial'],
			array( self::$oInstance, 'lastChanceBehaviorSwitches' )
		);

		//TODO: This does not seem to be the right place for stuff like this.
		global $wgFileExtensions;
		$aFileExtensions  = BsConfig::get( 'MW::FileExtensions' );
		$aImageExtensions = BsConfig::get( 'MW::ImageExtensions' );
		$wgFileExtensions = array_merge( $aFileExtensions, $aImageExtensions );
		$wgFileExtensions = array_values( array_unique( $wgFileExtensions ) );
	}

	/* Returns the filesystem path of the core installation
	 * @return String Filesystempath to the core installation
	 */

	public static function getFileSystemPath() {
		return BSROOTDIR;
	}

	/**
	 * Depending on the MediaWiki version, this method try to load the HtmlForm class.
	 * Everytime you want to use this class, you should call this method.
	 */
	public static function loadHtmlFormClass() {
		wfProfileIn('BS::' . __METHOD__);
		if (!self::$bHtmlFormClassLoaded) {
			self::$bHtmlFormClassLoaded = true;
			$compat = false;
			if (!class_exists('Html', true)) {
				include(__DIR__ . DS . 'Html.php');
			}
			if (!class_exists('HTMLForm', true)) {
				include(__DIR__ . DS . 'HTMLForm.php');
				$compat = true;
			}
		}
		wfProfileOut('BS::' . __METHOD__);
	}

	// todo msc 2011-04-27 wiederholter Aufruf führt schnell zu einem Speicherüberlauf (>128MB bei Indexierung)
	// scheinbar wird ausserhalb der Methode gecacht! Aufruf mit adapter->parseWikiText($text, true) schafft KEINE Abhilfe.
	public function parseWikiText( $sText, $oTitle, $nocache = false, $numberheadings = null ) {
		wfProfileIn('BS::' . __METHOD__);

		if ( !self::$oLocalParser ) self::$oLocalParser = new Parser();
		if ( !self::$oLocalParserOptions ) self::$oLocalParserOptions = new ParserOptions();

		if ( $numberheadings === false ) {
			self::$oLocalParserOptions->setNumberHeadings( false );
		} elseif ( $numberheadings === true ) {
			self::$oLocalParserOptions->setNumberHeadings( true );
		}

		// TODO MRG20110707: Check it this cannot be unified

		if ( $nocache ) self::$oLocalParser->disableCache();

		if ( !( $oTitle instanceof Title ) ) return '';

		$output = self::$oLocalParser->parse( $sText, $oTitle, self::$oLocalParserOptions, true )->getText();

		wfProfileOut('BS::' . __METHOD__);
		return $output;
	}

	public static function getUserDisplayName( $oUser = null ) {
		wfProfileIn( 'BS::'.__METHOD__ );
		global $wgUser;
		if ( $oUser === null ) {
			$oUser = $wgUser;
		}
		if ( !( $oUser instanceof User ) ) {
			wfProfileOut( 'BS::'.__METHOD__ );
			return false;
		}
		$sRealname = $oUser->getRealName();
		if ( $sRealname ) {
			wfProfileOut( 'BS::'.__METHOD__ );
			return $sRealname;
		} else {
			wfProfileOut( 'BS::'.__METHOD__ );
			return $oUser->getName();
		}
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
			if ( isset( $_SERVER['HTTP_X_REWRITE_URL'] ) ) { // check this first so IIS will catch
				$requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
			} elseif ( isset( $_SERVER['REQUEST_URI'] ) ) {
				$requestUri = $_SERVER['REQUEST_URI'];
			} elseif ( isset( $_SERVER['ORIG_PATH_INFO'] ) ) { // IIS 5.0, PHP as CGI
				$requestUri = $_SERVER['ORIG_PATH_INFO'];
				if ( !empty( $_SERVER['QUERY_STRING'] ) ) {
					$requestUri .= '?' . $_SERVER['QUERY_STRING'];
				}
			}
			self::$prRequestUri = $requestUri;
			self::$prUrlIsEncoded = ( urldecode( self::$prRequestUri ) != self::$prRequestUri );
		}
		if ( $getUrlEncoded ) {
			return ( self::$prUrlIsEncoded ? self::$prRequestUri : urlencode( self::$prRequestUri ) );
		}
		wfProfileOut( 'BS::'.__METHOD__ );
		return ( self::$prUrlIsEncoded ? urldecode( self::$prRequestUri ) : self::$prRequestUri );
	}

	// TODO MRG (09.12.10 11:21): Habe silent im Standard auf true gesetzt. Echo ist ohnehin nicht gut.
	/**
	 *
	 * @param string $sPermission
	 * @param string $sI18NInstanceKey
	 * @param string $sI18NMessageKey
	 * @param bool $bSilent
	 * @return bool
	 */
	public static function checkAccessAdmission( $sPermission = 'read', $sI18NInstanceKey = 'BlueSpice', $sI18NMessageKey = 'not_allowed', $bSilent = true ) {
		wfProfileIn('BS::' . __METHOD__);
		// TODO MRG28072010: isAllowed prüft nicht gegen die Artikel. D.H. die Rechte sind nicht per Namespace überprüfbar
		$oUser = self::loadCurrentUser();
		if ( $oUser->isAllowed( $sPermission ) ) {
			wfProfileOut('BS::' . __METHOD__);
			return true;
		}
		if ( !$bSilent ) echo wfMessage( 'bs-' . $sI18NMessageKey )->plain();

		wfProfileOut('BS::' . __METHOD__);
		return false;
	}

	public static function loadCurrentUser() {
		wfProfileIn('BS::' . __METHOD__);
		/* Load current user */
		global $wgUser;

		if ( !$wgUser || is_null( $wgUser->mId ) ) {

			if ( !is_null( self::$prCurrentUser ) ) {
				wfProfileOut('BS::' . __METHOD__);
				return self::$prCurrentUser;
			}

			self::$prCurrentUser = User::newFromSession();
			self::$prCurrentUser->load();
			wfProfileOut('BS::' . __METHOD__);
			return self::$prCurrentUser;
		}

		wfProfileOut('BS::' . __METHOD__);
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

	/**
	 * Creates a miniprofile for a user. It consists if the useres profile image
	 * and links to his userpage. In future versions it should also have a
	 * little menu with his mail adress, and other profile information.
	 * @param User $oUser The requested MediaWiki User object
	 * @param array $aParams The settings array for the mini profile view object
	 * @return ViewUserMiniProfile A view with the users mini profile
	 */
	public function getUserMiniProfile( $oUser, $aParams = array() ) {
		wfProfileIn('BS::' . __METHOD__);
		$sParamsHash = md5( serialize( $aParams ) );
		$sViewHash = $oUser->getName() . $sParamsHash;

		if ( isset( self::$aUserMiniProfiles[$sViewHash] ) ) {
			wfProfileOut('BS::' . __METHOD__);
			return self::$aUserMiniProfiles[$sViewHash];
		}

		$oUserMiniProfileView = new ViewUserMiniProfile();
		$oUserMiniProfileView->setOptions( $aParams );
		$oUserMiniProfileView->setOption( 'user', $oUser );

		wfRunHooks( 'BSAdapterGetUserMiniProfileBeforeInit', array( $oUserMiniProfileView, $oUser, $aParams ) );

		$oUserMiniProfileView->init();

		self::$aUserMiniProfiles[$sViewHash] = $oUserMiniProfileView;

		wfProfileOut('BS::' . __METHOD__);
		return $oUserMiniProfileView;
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
	public function registerPermission( $sPermissionName, $aUserGroups = array() ) {
		wfProfileIn('BS::' . __METHOD__);

		global $wgGroupPermissions, $wgAvailableRights;
		$wgGroupPermissions['sysop'][$sPermissionName] = true;

		foreach ( $aUserGroups as $sGroup ) {
			// check if it is not set already
			if ( !isset( $wgGroupPermissions[$sGroup][$sPermissionName] ) ) {
				$wgGroupPermissions[$sGroup][$sPermissionName] = true;
			}
		}

		$wgAvailableRights[] = $sPermissionName;

		wfProfileOut('BS::' . __METHOD__);
	}

	public function registerBehaviorSwitch( $sMagicWord, $aCallback = null ) {
		if ( is_callable( $aCallback ) ) {
			$this->aBehaviorSwitches[$sMagicWord] = $aCallback;
		} elseif ( !isset( MagicWord::$mDoubleUnderscoreIDs[$sMagicWord] ) ) {
			MagicWord::$mDoubleUnderscoreIDs[] = $sMagicWord;
		}
	}

	// TODO MRG (01.12.10 00:07): Ich bezweifle, dass wir diese Funktion brauchen
	public function behaviorSwitches( &$article, &$content ) {
		// TODO SW(05.01.12 15:37): Profiling
		if ( !isset( $this->aBehaviorSwitches ) )
			return true;

		$sNowikistripped = preg_replace( "/<nowiki>.*?<\/nowiki>/i", "", $content );
		foreach ( $this->aBehaviorSwitches as $sSwitch => $sCallback ) {
			if ( strstr( $sNowikistripped, '__' . $sSwitch . '__' ) ) {
				call_user_func( $sCallback );
			}
		}
		return true;
	}

	public function hideBehaviorSwitches( &$parser, &$text ) {
		// TODO SW(05.01.12 15:37): Profiling
		if ( !isset( $this->aBehaviorSwitches ) ) return true;

		$sNowikistripped = preg_replace( "/<nowiki>.*?<\/nowiki>/i", "", $text );
		foreach ( $this->aBehaviorSwitches as $sSwitch => $sCallback ) {
			if ( strstr( $sNowikistripped, '__' . $sSwitch . '__' ) ) {
				call_user_func( $sCallback );
			}
			// TODO MRG (01.12.10 00:08): Wahrscheinlich kann man das auch gleich beim ersten preg_replace machen
			$text = preg_replace( "/(<nowiki>.*?)__{$sSwitch}__(.*?<\/nowiki>)/i", "$1@@{$sSwitch}@@$2", $text );
		}
		return true;
	}

	public function recoverBehaviorSwitches( &$parser, &$text ) {
		// TODO SW(05.01.12 15:38): Profiling
		if ( !isset( $this->aBehaviorSwitches ) ) return true;

		foreach ( $this->aBehaviorSwitches as $sSwitch => $sCallback ) {
			$text = str_replace( '__' . $sSwitch . '__', "", $text );
			$text = preg_replace( "/@@" . $sSwitch . "@@/", '__' . $sSwitch . '__', $text );
		}
		return true;
	}

	/**
	 * Needed for edit and sumbit (preview) mode
	 * @param <type> $editPage
	 * @return <type>
	 */
	public function lastChanceBehaviorSwitches( $editPage ) {
		// TODO SW(05.01.12 15:39): Profiling
		$sContent = BsPageContentProvider::getInstance()->getContentFromTitle( RequestContext::getMain()->getTitle() );
		if ( !isset( $this->aBehaviorSwitches ) ) return true;

		$sNowikistripped = preg_replace( "/<nowiki>.*?<\/nowiki>/mi", "", $sContent );
		foreach ( $this->aBehaviorSwitches as $sSwitch => $sCallback ) {
			if ( strstr( $sNowikistripped, '__' . $sSwitch . '__' ) ) {
				call_user_func( $sCallback );
			}
		}
		// TODO: This note should be displayed when the editor is deactivated
		//$editPage->editFormTextTop = "Der Editor wurde deaktiviert <br/>";
		if ( isset( $editPage->textbox1 ) ) {
			foreach ( $this->aBehaviorSwitches as $sSwitch => $sCallback ) {
				$sNowikistripped = preg_replace( "/<nowiki>.*?<\/nowiki>/mi", "", $editPage->textbox1 );
				if ( strstr( $sNowikistripped, '__' . $sSwitch . '__' ) ) {
					call_user_func( $sCallback );
				}
			}
		}
		return true;
	}

	public static function ajaxBSPing() {
		$aResult = array(
			"success" => false,
			"errors" => array(),
			"message" => '',
		);

		$oRequest = RequestContext::getMain()->getRequest();
		$iArticleId  = $oRequest->getInt( 'iArticleID', 0 );
		$iNamespace  = $oRequest->getInt( 'iNamespace', 0 );
		$sTitle      = $oRequest->getVal( 'sTitle', '' );
		$iRevision   = $oRequest->getInt( 'iRevision', 0 );
		$aBSPingData = $oRequest->getArray( 'BsPingData', array() );

		$aResult['success'] = true;
		foreach ( $aBSPingData as $aSinglePing ) {
			if ( empty( $aSinglePing['sRef'] ) ) continue;
			if ( !$aResult['success'] ) break;

			if ( !isset( $aSinglePing['aData'] ) )
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

		return json_encode( $aResult );
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

	public static function getClientScriptBlocks() {
		return self::$aClientScriptBlocks;
	}

	/**
	 * Use this to place javascript logic _below_ the including script files. Therefore you can benefit from the available frameworks like BlueSpiceFramework, ExtJS and jQuery.
	 * @param String $sExtensionKey The name of the extension. This is just for creating a nice comment within the script-Tags
	 * @param String $sCode The JavaScript code, that should be executed after all scriptfiles have been included
	 * @param String $sUniqueKey (Optional) If provided the script block gets saved in with a unique key and therefore will not be registered multiple times.
	 * @deprecated Use MediaWikis Outpage interface instead
	 */
	public static function registerClientScriptBlock( $sExtensionKey, $sCode, $sUniqueKey = '' ) {
		wfDeprecated(__METHOD__);
		if( !empty( $sUniqueKey ) ) {
			self::$aClientScriptBlocks[$sUniqueKey] = array( $sExtensionKey, $sCode );
		} else {
			self::$aClientScriptBlocks[] = array( $sExtensionKey, $sCode );
		}
	}

}