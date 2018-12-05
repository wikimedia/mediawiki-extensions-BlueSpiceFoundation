<?php

/**
 * This file contains the BsCore class.
 *
 * The BsCore class is the main class of the BlueSpice framework.
 * It controlls the whole life sequence of the framework.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
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
 * This file is part of BlueSpice MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Sebastian Ulbricht <sebastian.ulbricht@dragon-design.hk>
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @author     Stephan Muggli <muggli@hallowelt.com>
 * @package    Bluespice_Core
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
use MediaWiki\MediaWikiServices;

/**
 * The BsCore
 * @package BlueSpice_Core
 * @subpackage Core
 */
class BsCore {

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
	 * Simple caching mechanism for UserMiniProfiles
	 * @var array
	 */
	protected static $aUserMiniProfiles = array();

	protected static $bHtmlFormClassLoaded = false;

	public static function getForbiddenCharsInArticleTitle() {
		return self::$prForbiddenCharsInArticleTitle;
	}

	/**
	 * Used to access the singleton BlueSpice object.
	 * @return BsCore Singleton instance of BlueSpice object.
	 */
	public static function getInstance() {
		if ( self::$oInstance === null ) {
			self::$oInstance = new BsCore();
		}
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
				$oDb = wfGetDB( DB_REPLICA );
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
	 * @param string $key
	 * @param array|null $default A default array that gets returned, if the
	 * submitted array is not valid or does not match the requested BsPARAMTYPE.
	 * @param BsPARAMTYPE|null $options Sets the type of the expected return value.
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

	/* Returns the filesystem path of the core installation
	 * @return String Filesystempath to the core installation
	 */

	public static function getFileSystemPath() {
		return BSROOTDIR;
	}

	/**
	 * Parses WikiText into HTML
	 * @param string $sText WikiText
	 * @param Title $oTitle
	 * @param bool $nocache DISFUNCTIONAL and therefore DEPRECATED. There is no chaching anyway.
	 * @param bool|null $numberheadings
	 * @return string The HTML result
	 */
	public function parseWikiText( $sText, $oTitle, $nocache = false, $numberheadings = null ) {
		if ( !self::$oLocalParser ) self::$oLocalParser = new Parser();
		if ( !self::$oLocalParserOptions ) self::$oLocalParserOptions = new ParserOptions();

		if ( $numberheadings === false ) {
			self::$oLocalParserOptions->setNumberHeadings( false );
		} elseif ( $numberheadings === true ) {
			self::$oLocalParserOptions->setNumberHeadings( true );
		}

		// TODO MRG20110707: Check it this cannot be unified

		if ( $nocache ) {
			wfDebug( __METHOD__.': Use of $nocache parameter is deprecated. There is no caching anyway.' );
		}

		if ( !( $oTitle instanceof Title ) ) return '';

		$output = self::$oLocalParser->parse( $sText, $oTitle, self::$oLocalParserOptions, true )->getText();

		return $output;
	}

	/**
	 * Determines the request URI for Apache and IIS
	 *
	 * @param bool $getUrlEncoded set to true to get URI url encoded
	 * @return string the requested URI
	 */
	public static function getRequestURI($getUrlEncoded = false) {
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
		return ( self::$prUrlIsEncoded ? urldecode( self::$prRequestUri ) : self::$prRequestUri );
	}

	// TODO MRG (09.12.10 11:21): Habe silent im Standard auf true gesetzt. Echo ist ohnehin nicht gut.
	/**
	 * @deprecated since version 3.0.0 - use \Title->userCan() or
	 * \User->isAllowed()
	 * @param string $sPermission
	 * @param string $sI18NInstanceKey
	 * @param string $sI18NMessageKey
	 * @param bool $bSilent
	 * @return bool
	 */
	public static function checkAccessAdmission( $sPermission = 'read', $sI18NInstanceKey = 'BlueSpice', $sI18NMessageKey = 'not_allowed', $bSilent = true ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		// TODO MRG28072010: isAllowed prüft nicht gegen die Artikel. D.H. die Rechte sind nicht per Namespace überprüfbar
		$oUser = self::loadCurrentUser();
		if ( $oUser->isAllowed( $sPermission ) ) {
			return true;
		}
		if ( !$bSilent ) echo wfMessage( 'bs-' . $sI18NMessageKey )->plain();

		return false;
	}

	/**
	 * @deprecated since version 3.0.0 - this probably only in use by the core
	 * it self - anyway, get your own user ;)
	 * @return \User
	 */
	public static function loadCurrentUser() {
		return \RequestContext::getMain()->getUser();
	}

	/**
	 * Creates a miniprofile for a user. It consists if the useres profile image
	 * and links to his userpage. In future versions it should also have a
	 * little menu with his mail adress, and other profile information.
	 * @deprecated since version 3.0.0 - Use
	 * \BlueSpice\Services::getInstance()->getBSRendererFactory() and create
	 * an instance of the 'userimage' renderer.
	 * ->get( 'userimage', new \BlueSpice\Renderer\Params( [...] )
	 * @param User $oUser The requested MediaWiki User object
	 * @param array $aParams The settings array for the mini profile view object
	 * @return ViewUserMiniProfile A view with the users mini profile
	 */
	public function getUserMiniProfile( $oUser, $aParams = array() ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$sParamsHash = md5( serialize( $aParams ) );
		$sViewHash = $oUser->getName() . $sParamsHash;

		if ( isset( self::$aUserMiniProfiles[$sViewHash] ) ) {
			return self::$aUserMiniProfiles[$sViewHash];
		}

		$oUserMiniProfileView = new ViewUserMiniProfile();
		$oUserMiniProfileView->setOptions( $aParams );
		$oUserMiniProfileView->setOption( 'user', $oUser );

		Hooks::run( 'BSCoreGetUserMiniProfileBeforeInit', array( &$oUserMiniProfileView, &$oUser, &$aParams ) );

		$oUserMiniProfileView->init();

		self::$aUserMiniProfiles[$sViewHash] = $oUserMiniProfileView;

		return $oUserMiniProfileView;
	}

	/**
	 * Registers a permission with the MediaWiki Framework.
	 * object for proper internationalisation of your permission. If not already set,
	 * every permission is granted automatically to the user group 'sysop'. You can
	 * specify additional groups through the third parameter.
	 *
	 * @deprecated since version 3.0 - use BSPermissionRegistry instead
	 *
	 * @param String $sPermissionName I.e. 'myextension-dosomething'
	 * @param Array $aUserGroups User groups that get preinitialized with the new
	 * pemission. I.e. array( 'user', 'bureaucrats' )
	 * @param Array $aConfig set configs for permissions i.e. array('type'=>'global').
	 * The default here is ('type' = 'namespace')
	 * @return void
	 */
	public function registerPermission( $sPermissionName, $aUserGroups = array(), $aConfig = array() ) {
		global $wgGroupPermissions, $wgAvailableRights, $bsgPermissionConfig;

		$aUserGroups = array_merge( [
			'sysop'
		], $aUserGroups );
		if(!isset($bsgPermissionConfig[$sPermissionName])){
			if ( isset( $aConfig ) ) {
				$bsgPermissionConfig[$sPermissionName] = $aConfig;
			} else {
				$bsgPermissionConfig[$sPermissionName] = array( 'type' => 'namespace' );
			}
		}
		foreach ( $aUserGroups as $sGroup ) {
			if ( !isset( $wgGroupPermissions[$sGroup][$sPermissionName] ) ) {
				$wgGroupPermissions[$sGroup][$sPermissionName] = true;
			}
		}
		$wgAvailableRights[] = $sPermissionName;

		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return true;
	}

	/**
	 * DEPRECATED!
	 * Register a callback for a MagicWord
	 * @deprecated since version 3.0.0 - Use proper Hook registration instead
	 * @param string $sMagicWord The MagicWord in upper case and without
	 * surrounding double underscores. OR: if $callback == null this may be a
	 * lower case identifier that gets written to the page_props table by the
	 * parser.
	 * @param callable $aCallback or null to use MediaWiki page_props mechanism
	 */
	public function registerBehaviorSwitch( $sMagicWord, $aCallback = null ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		if ( is_callable( $aCallback ) ) {
			return;
		} else {
			global $wgHooks;
			$wgHooks['GetDoubleUnderscoreIDs'][] = function ( &$ids ) use ( $sMagicWord ) {
				if ( !in_array( $sMagicWord, $ids ) ) {
					$ids[] = $sMagicWord;
				}
			};
		}
	}

	/**
	 * Make the page being parsed have a dependency on $page via the templatelinks table.
	 * https://www.mediawiki.org/wiki/Manual:Tag_extensions#Regenerating_the_page_when_another_page_is_edited
	 * @param Parser $oParser
	 * @param String $sTitle
	 */
	public static function addTemplateLinkDependencyByText($oParser, $sTitle) {
		$oTitle = Title::newFromText( $sTitle );
		static::addTemplateLinkDependency($oParser, $oTitle);
	}

	/**
	 * Make the page being parsed have a dependency on $page via the templatelinks table.
	 * https://www.mediawiki.org/wiki/Manual:Tag_extensions#Regenerating_the_page_when_another_page_is_edited
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
		global $IP;
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
}
