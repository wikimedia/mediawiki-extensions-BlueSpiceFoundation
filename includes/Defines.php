<?php

//TDOD: Is this really still necessary?
if (!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);
elseif (DS != DIRECTORY_SEPARATOR) {
	$message = 'Constant "DS" already in use but unequal "DIRECTORY_SEPARATOR", namely: DS == "' . DS . '"';
	//throw new Exception($message);
	exit($message . ' in ' . __FILE__ . ', line ' . __LINE__);
}

// TODO MRG20100724: Ist das ok beim Hosting (index.php ist hier symlinked)
/* Lilu:
 * Bei Symlinks wird es Probleme geben, da __FILE__ den absoluten Pfad inkl. aufgelöster Symlinks enthält.
 * Lösung wäre für das Hosting ein gemeinsam genutzter Core mit separater Konfiguration pro Präsenz.
 * Dies sollte sich ohne Probleme umsetzen lassen, da BlueSpice ja so designed ist, dass der Core in einem
 * separaten Verzeichnis liegen kann.
 */
if (!defined('WIKI_FARMING')) {
	define('BSROOTDIR', dirname(__DIR__) );
	define('BSCONFIGDIR', BSROOTDIR . DS . 'config');
	define('BSDATADIR',   BSROOTDIR . DS . 'data'); //Present

	//New constants
	$sTMPUploadDir  = empty($GLOBALS['wgUploadDirectory'])    ? $GLOBALS['IP'] . DS . 'images'           : $GLOBALS['wgUploadDirectory'];
	$sTMPCacheDir   = empty($GLOBALS['wgFileCacheDirectory']) ? $sTMPUploadDir . DS . 'cache' : $GLOBALS['wgFileCacheDirectory'];
	$sTMPUploadPath = empty($GLOBALS['wgUploadPath']) ? $GLOBALS['wgScriptPath'] . "/images" : $GLOBALS['wgUploadPath'];

	define('BS_DATA_DIR',  $sTMPUploadDir. DS . 'bluespice'); //Future
	define('BS_CACHE_DIR', $sTMPCacheDir. DS . 'bluespice'); //$wgCacheDirectory?
	define('BS_DATA_PATH', $sTMPUploadPath. '/bluespice');
}

if (!defined('BS_NS_OFFSET')) {
	define('BS_NS_OFFSET', 1500);
}
