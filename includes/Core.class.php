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
 * @version    0.1.0
 * @version    $Id: Core.class.php 9864 2013-06-24 09:03:07Z rvogel $
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

	protected static $prForbiddenCharsInArticleTitle = array('#', '<', '>', '[', ']', '|', '{', '}');
	/**
	 * an array of adapter instances
	 * @var array
	 */
	protected static $prInstances = array();

	/**
	 * an associative array of classnames and their paths for autoloading classes, registered by extensions
	 * @var array
	 */
	protected static $prAutoloadRegisterExt = array();

	/**
	 * an associative array of classnames and their paths for the core classes.
	 * @var array
	 */
	protected static $prAutoloadRegister = array();

	/**
	 * a state flag if ExtJs is already loaded
	 * @var bool
	 */
	protected static $bExtJsLoaded = false;

	/* Lilu:
	 * Bis hierher ist alles statischer Code. Sprich Properties und statische Methoden.
	 * Nachfolgender Code ist nicht-statisch und bezieht sich auf Objekt-Instanzen.
	 *
	 */
	protected $mAdapter = null;

	/**
	 * The constructor is protected because of the singleton pattern. Use the
	 * static BsCore::getInstance( $adapter ) method to retrieve a instance.
	 * @param string $adapter Name of requested adapter. For example "MW", "WP",
	 *  "ELGG".
	 */
	protected function __construct( $adapter ) {
		wfProfileIn('Performance: ' . __METHOD__);
		global $IP;

		// TODO MRG20100726: Kann eine Bs-Klasse nur mit einem Adapter geladen werden?
		BsConfig::registerVar( 'Core::BlueSpiceScriptPath', '', BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_STRING | BsConfig::RENDER_AS_JAVASCRIPT, 'bs-pref-BlueSpiceScriptPath' );
		BsConfig::registerVar( 'Core::Applications', array(), BsConfig::LEVEL_PRIVATE | BsConfig::TYPE_ARRAY_MIXED, 'bs-Applications' );
		BsConfig::registerVar( 'Core::ApplicationContext', '', BsConfig::LEVEL_PRIVATE | BsConfig::TYPE_STRING | BsConfig::RENDER_AS_JAVASCRIPT, 'bs-pref-ApplicationContext' );
		BsConfig::registerVar( 'Core::TestMode', false, BsConfig::LEVEL_PUBLIC | BsConfig::TYPE_BOOL, 'bs-pref-TestMode', 'toggle' );

		BsConfig::set('Core::RootPath', $IP);
		BsAdapter::registerAdapter('MW', dirname(__FILE__).DS.'adapter');

		$this->mAdapter = BsAdapter::loadAdapter( $adapter );
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

	/**
	 * registers a class for the autoloader
	 * @param string $name the class name
	 * @param string $path the path to the directory which contains the classfile
	 * @param string $file the class filename, if it don't corresponds to the naming conventions
	 */
	public static function registerClass($name, $path, $file = false) {
		wfProfileIn('Performance: ' . __METHOD__);
		if (!$file) {
			$file = ( strpos($name, 'Bs') === 0 ) ? substr($name, 2) : $name;
			$file .= '.class.php';
		}
		self::$prAutoloadRegisterExt[$name] = $path . DS . $file;
		wfProfileOut('Performance: ' . __METHOD__);
	}

	/**
	 * registers a interface for the autoloader
	 * @param string $name the interface name
	 * @param string $path the path to the directory which contains the interfacefile
	 * @param string $file the interface filename, if it don't corresponds to the naming conventions
	 */
	public static function registerInterface($name, $path, $file = false) {
		wfProfileIn('Performance: ' . __METHOD__);
		if (!$file) {
			$file = ( strpos($name, 'Bs') === 0 ) ? substr($name, 2) : $name;
			$file .= '.interface.php';
		}
		self::$prAutoloadRegisterExt[$name] = $path . DS . $file;
		wfProfileOut('Performance: ' . __METHOD__);
	}

	public static function getForbiddenCharsInArticleTitle() {
		return self::$prForbiddenCharsInArticleTitle;
	}

	/**
	 * Used to access the singleton BlueSpice object.
	 * @param string $adapter Name of requested adapter. For example "MW", "WP",
	 *  "ELGG".
	 * @return BsCore Singleton instance of BlueSpice object.
	 */
	public static function getInstance($adapter) {
		wfProfileIn('Performance: ' . __METHOD__);
		if (!isset(self::$prInstances[$adapter]) || self::$prInstances[$adapter] === NULL) {
			self::$prInstances[$adapter] = new BsCore($adapter);
		}
		wfProfileOut('Performance: ' . __METHOD__);
		return self::$prInstances[$adapter];
	}

	/**
	 * This is the default interface to retrieve information from super global
	 * arrays like $_REQUEST[] or $_SESSSION[].
	 * @param string $key The name of the requested parameter.
	 * @param mixed $default If the requested parameter is not set, this default
	 * value is going to be returned.
	 * @param mixed $options A bitwise assigned combination of PARAM, BsPARAMTYPE
	 * and BsPARAMOPTION.
	 * For Example 'PARAM::REQUEST|BsPARAMTYPE::STRING|BsPARAMOPTION::DEFAULT_ON_ERROR'
	 * @return mixed Depending on the submitted options the result is a
	 * sanitized value of a parameter or a default vaule, if the requested key
	 * is not set.
	 */
	public static function getParam($key, $default = false, $options = NULL) {
		// PHP or NetBeans seem to not allow bitwise assignment operators in the arguments of a function
		// TODO MRG20100724: Die Defaultwerte mÃ¼ssen einzeln gesetzt werden. Ich kann ja PARAM::GET als alleinige Option angeben. Das liefert momentan $default
		/* Lilu:
		 * Das ist definiertes Verhalten. Wenn kein Typen-Parameter angegeben wird, dann kann auch nicht geprÃ¼ft werden.
		 * Da keine ungeprÃ¼ften Daten in das System gelangen sollen, wird dem entsprechend der Default-Wert zurÃ¼ckgegeben.
		 */
		if ($options === NULL)
			$options = BsPARAM::REQUEST | BsPARAMTYPE::STRING | BsPARAMOPTION::DEFAULT_ON_ERROR;

		// TODO MRG20100724: Was passiert, wenn
		// a) PARAM::XXX garnicht gesetzt ist
		// b) PARAM::XXX nicht existiert
		// c) Kombinationen, z.B: PARAM::GET & PARAM::FILES verwendet werden ?
		// Wahrscheinlich muss hier hinter jedem Typ ein isset-check gemacht werden. Und wir mÃ¼ssen eine PrÃ¤zendenzregel festlegen (POST vor GET oder umgekehrt?)
		/* Lilu:
		 * Ist kein PARAM-Parameter gesetzt, dann greift die isset-PrÃ¼fung nach diesem Block und der Default-Wert wird zurÃ¼ckgegeben.
		 * Wird eine PARAM-Konstante verwenden, die nicht definiert ist, so wirft PHP eine Notice und auch dann greift die isset-PrÃ¼fung.
		 * Bei Kombinationen verschiedener PARAM-Parameter wird der Parameter mit dem ersten Treffer genutzt.
		 */
		if ($options & BsPARAM::REQUEST) {
			$params = & $_REQUEST;
		} elseif ($options & BsPARAM::GET) {
			$params = & $_GET;
		} elseif ($options & BsPARAM::POST) {
			$params = & $_POST;
		} elseif ($options & BsPARAM::FILES) {
			$params = & $_FILES;
		} elseif ($options & BsPARAM::COOKIE) {
			$params = & $_COOKIE;
		} elseif ($options & BsPARAM::SESSION) {
			$params = & $_SESSION;
		} elseif ($options & BsPARAM::SERVER) {
			$params = & $_SERVER;
		}

		if (!isset($params[$key]) || $params[$key] === NULL) {
			return $default;
		}
		// TODO MRG20100724:Ich versteh die Beschriftung nicht. kann man das sprechender machen? Was ist die Alternative? false_on_error?
		/* Lilu:
		 * DEFAULT_ON_ERROR steht dafÃ¼r, dass nicht versucht werden soll, den Wert mit dem Sanitizer in ein passendes Format umzuwandeln,
		 * sondern im Fehlerfall einfach der definierte Defaultwert zurÃ¼ckgegeben werden soll.
		 */
		if (!( $options & BsPARAMOPTION::DEFAULT_ON_ERROR )) {
			return self::sanitize($params[$key], $default, $options);
		}

		// TODO MRG20100724: Theoretisch kÃ¶nnte ich BsPARAMTYPE::RAW & BsPARAMTYPE::NUMERIC Ã¼bergeben. Ist hier das Verhalten definiert? Bitte hier als Kommentar angeben
		/* Lilu:
		 * Wenn mehrere TypeBits gesetzt werden, wird der erste Treffer ausgefÃ¼hrt.
		 * Bei BsPARAMTYPE::RAW & BsPARAMTYPE::NUMERIC wÃ¼rde also nur auf BsPARAMTYPE::RAW geprÃ¼ft, da dies schon ein Treffer ist.
		 */
		if ($options & BsPARAMTYPE::RAW) {
			return $params[$key];
		}
		if ($options & BsPARAMTYPE::ARRAY_MIXED && is_array($params[$key])) {
			return $params[$key];
		}
		if ($options & BsPARAMTYPE::NUMERIC && is_numeric($params[$key])) {
			return $params[$key];
		}
		if ($options & BsPARAMTYPE::INT && is_int($params[$key])) {
			return $params[$key];
		}
		if ($options & BsPARAMTYPE::FLOAT && is_float($params[$key])) {
			return $params[$key];
		}
		if ($options & BsPARAMTYPE::BOOL) {
			if ($params[$key] == 'false'
					|| $params[$key] == '0'
					|| $params[$key] == '')
				$params[$key] = false;
			if ($params[$key] == 'true'
					|| $params[$key] == '1')
				$params[$key] = true;
			if (is_bool($params[$key])) {
				return $params[$key];
			}
			return (bool) $params[$key];
		}
		if ($options & BsPARAMTYPE::STRING && is_string($params[$key])) {
			return $params[$key];
		}

		if ($options & BsPARAMTYPE::ARRAY_NUMERIC && is_array($params[$key])) {
			foreach ($params[$key] as $k => $v) {
				if (!is_numeric($v)) {
					$params[$key][$k] = NULL;
				}
			}
			return $params[$key];
		}
		// TODO MRG (29.01.11 23:37): Hier werden keine negativen nummern durchgelassen. das ist ein Problem
		if ($options & BsPARAMTYPE::ARRAY_INT && is_array($params[$key])) {
			foreach ($params[$key] as $k => $v) {
				if (!is_int($v)) {
					$params[$key][$k] = NULL;
				}
			}
			return $params[$key];
		}
		if ($options & BsPARAMTYPE::ARRAY_FLOAT && is_array($params[$key])) {
			foreach ($params[$key] as $k => $v) {
				if (!is_float($v)) {
					$params[$key][$k] = NULL;
				}
			}
			return $params[$key];
		}
		if ($options & BsPARAMTYPE::ARRAY_BOOL && is_array($params[$key])) {
			foreach ($params[$key] as $k => $v) {
				if (!is_bool($v)) {
					$params[$key][$k] = NULL;
				}
			}
			return $params[$key];
		}
		if ($options & BsPARAMTYPE::ARRAY_STRING && is_array($params[$key])) {
			foreach ($params[$key] as $k => $v) {
				if (!is_string($v)) {
					$params[$key][$k] = NULL;
				}
			}
			return $params[$key];
		}

		return $default;
	}

	/**
	 *
	 * @param int $iOptions
	 * @return array
	 */
	public static function getParams($iOptions) {
		// TODO RBV (02.07.11 16:47):  implement in a sane way
		switch ($iOptions) {
			case BsPARAM::GET:
				return self::sanitize($_GET, array(), BsPARAMTYPE::ARRAY_STRING);
				break;
			case BsPARAM::POST:
				return self::sanitize($_POST, array(), BsPARAMTYPE::ARRAY_STRING);
				break;
			default:
				return self::sanitize($_REQUEST, array(), BsPARAMTYPE::ARRAY_STRING);
				break;
		}
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
		 * Beim Sanitizen der Default-Werte entsteht sonst ggf. das Problem, das wir keinen gÃ¼ltigen Wert zurÃ¼ckgeben kÃ¶nnen. (NULL?)
		 * Das wÃ¼rde einen groÃŸen Vorteil des Sanitizers (die nicht mehr benÃ¶tigte GÃ¼ltigkeitsprÃ¼fung) wieder aushebeln.
		 */
		if ($options & BsPARAMTYPE::RAW) {
			return $handover;
		}
		if ($options & BsPARAMTYPE::ARRAY_MIXED) {
			if (is_array($handover)) {
				return $handover;
			}
			return array($handover);
		}
		if ($options & BsPARAMTYPE::NUMERIC) {
			if (is_numeric($handover)) {
				return $handover;
			}
			return floatval($handover);
		}
		if ($options & BsPARAMTYPE::INT) {
			if (is_int($handover)) {
				return $handover;
			}
			return intval($handover); // TODO RBV (06.12.10 09:55): If $handover is 'abc' intval() will return 0. This is not desired. The default value should be returned!
		}
		if ($options & BsPARAMTYPE::FLOAT) {
			if (is_float($handover)) {
				return $handover;
			}
			return floatval($handover);
		}
		if ($options & BsPARAMTYPE::BOOL) {
			if ($handover == 'false'
					|| $handover == '0'
					|| $handover == '')
				$handover = false;
			if ($handover == 'true'
					|| $handover == '1')
				$handover = true;
			if (is_bool($handover)) {
				return $handover;
			}
			return (bool) $handover;
		}
		if ($options & BsPARAMTYPE::STRING) {
			if (is_string($handover)) {
				if ($options & BsPARAMOPTION::CLEANUP_STRING) {
					return addslashes(strip_tags($handover));
				}
				return $handover;
			}
		}
		if ($options & BsPARAMTYPE::SQL_STRING) {
			if (is_string($handover)) {
				return addslashes($handover);
				// TODO: real_escape_string needs an oben database connection. We need a different method here.
				/* Lilu:
				 * Eine offene Datenbankverbindung sollte ab jetzt immer existieren, da der BlueSpice-Core seine Datenbankverbindung ziemlich frÃ¼h initialisiert.
				 */
				return mysqli_real_escape_string($handover);
			}
		}
		if ($options & BsPARAMTYPE::ARRAY_NUMERIC && is_array($handover)) {
			foreach ($handover as $k => $v) {
				if (!is_numeric($v)) {
					$handover[$k] = NULL;
				}
			}
			return $handover;
		}
		if ($options & BsPARAMTYPE::ARRAY_INT && is_array($handover)) {
			foreach ($handover as $key => $v) {
				if (!is_int($v)) {
					$handover[$key] = NULL;
				}
			}
			return $handover;
		}
		if ($options & BsPARAMTYPE::ARRAY_FLOAT && is_array($handover)) {
			foreach ($handover as $key => $v) {
				if (!is_float($v)) {
					$handover[$key] = NULL;
				}
			}
			return $handover;
		}
		if ($options & BsPARAMTYPE::ARRAY_BOOL && is_array($handover)) {
			foreach ($handover as $key => $v) {
				if (!is_bool($v)) {
					$handover[$key] = NULL;
				}
			}
			return $handover;
		}
		if ($options & BsPARAMTYPE::ARRAY_STRING && is_array($handover)) {
			foreach ($handover as $key => $v) {
				if (!is_string($v)) {
					$handover[$key] = NULL;
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

	/**
	 * Initializes the Adapter
	 */
	public function setup() {
		global $wgExtensionFunctions, $wgGroupPermissions, $wgWhitelistRead, $wgMaxUploadSize,
		$wgNamespacePermissionLockdown, $wgSpecialPageLockdown, $wgActionLockdown, $wgNonincludableNamespaces,
		$wgExtraNamespaces, $wgContentNamespaces, $wgNamespacesWithSubpages, $wgNamespacesToBeSearchedDefault,
		$wgLocalisationCacheConf, $wgAutoloadLocalClasses, $wgFlaggedRevsNamespaces, $wgNamespaceAliases, $wgVersion;

		wfProfileIn('Performance: ' . __METHOD__);

		wfProfileIn('Performance: ' . __METHOD__ . ' - Init Databases');
		BsDatabase::getInstance('Core::Database');
		wfProfileOut('Performance: ' . __METHOD__ . ' - Init Databases');

		wfProfileIn('Performance: ' . __METHOD__ . ' - Load Settings');
		if ( !defined( 'DO_MAINTENANCE' ) ) {
			BsConfig::loadSettings();
		}
		wfProfileOut('Performance: ' . __METHOD__ . ' - Load Settings');

		BsScriptManager::init();
		BsStyleManager::init();

		//Load allviews from BlueSpiceFoundation/lib/outputhandler/views.
		wfProfileIn('Performance: ' . __METHOD__ . ' - Load Views');
		BsOutputHandler::loadViews();
		wfProfileOut('Performance: ' . __METHOD__ . ' - Load Views');

		$sConfigPath = BSROOTDIR . DS . 'config';

		if ( file_exists( $sConfigPath . DS . 'nm-settings.php' ) ) {
			include( $sConfigPath . DS . 'nm-settings.php' );
		}
		if ( file_exists( $sConfigPath . DS . 'gm-settings.php' ) ) {
			include( $sConfigPath . DS . 'gm-settings.php' );
		}
		if ( file_exists( $sConfigPath . DS . 'pm-settings.php' ) ) {
			include( $sConfigPath . DS . 'pm-settings.php');
		}

		wfProfileOut('Performance: ' . __METHOD__);
	}

	/**
	 * Getter method for the current adapter.
	 * @return Adapter Reference to the current Adapter object.
	 */
	public function getAdapter() {
		return $this->mAdapter;
	}

	/**
	 * BlueSpice Autoloader
	 * Should never be called directly.
	 * @param String $classname
	 */
	public static function autoload($classname) {
		wfProfileIn('Performance: ' . __METHOD__);
		if (in_array($classname, array('ViewCountUpdate', 'ViewSystemGift', 'ViewSystemGifts', 'ViewGift', 'ViewGifts'))) {
			wfProfileOut('Performance: ' . __METHOD__);
			return; // TODO RBV (02.12.11 11:14): Refactor OutputHandler/View classes to avoid error.
		}

			
// TODO MRG20100726: Um die Requires einen try-catch.block tun und die Fehler abfangen
		$includePath = '';
		if (isset(self::$prAutoloadRegister[$classname])) {
			$includePath = BSROOTDIR . DS . 'includes' .DS . self::$prAutoloadRegister[$classname];
		} elseif (isset(self::$prAutoloadRegisterExt[$classname])) {
			$includePath = self::$prAutoloadRegisterExt[$classname];
		}

		if (empty($includePath)) {
			wfProfileOut('Performance: ' . __METHOD__);
			return;
		}
		
		include($includePath);
		wfProfileOut('Performance: ' . __METHOD__);
	}

	/* Returns the filesystem path of the core installation
	 * @return String Filesystempath to the core installation
	 */

	public static function getFileSystemPath() {
		return BSROOTDIR;
	}

	protected static $bHtmlFormClassLoaded = false;

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
				include(dirname(__FILE__) . DS . 'Html.php');
			}
			if (!class_exists('HTMLForm', true)) {
				include(dirname(__FILE__) . DS . 'HTMLForm.php');
				$compat = true;
			}
		}
		wfProfileOut('BS::' . __METHOD__);
	}

}
