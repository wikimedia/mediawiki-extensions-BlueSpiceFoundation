<?php


// TODO MRG20100724: Ist das ok beim Hosting (index.php ist hier symlinked)
/* Lilu:
 * Bei Symlinks wird es Probleme geben, da __FILE__ den absoluten Pfad inkl. aufgelöster Symlinks enthält.
 * Lösung wäre für das Hosting ein gemeinsam genutzter Core mit separater Konfiguration pro Präsenz.
 * Dies sollte sich ohne Probleme umsetzen lassen, da BlueSpice ja so designed ist, dass der Core in einem
 * separaten Verzeichnis liegen kann.
 */
if (!defined('WIKI_FARMING')) {
	if (!defined('BSROOTDIR')) {
		define('BSROOTDIR', dirname(__DIR__) );
        }
	if (!defined('BSCONFIGDIR')) {
		define( 'BSCONFIGDIR', BSROOTDIR . DIRECTORY_SEPARATOR . 'config' );
	}
	if (!defined('BSDATADIR')) {
		define( 'BSDATADIR',   BSROOTDIR . DIRECTORY_SEPARATOR . 'data' ); //Present
	}

	//New constants
	$sTMPUploadDir  = empty( $GLOBALS['wgUploadDirectory'] )
		? $GLOBALS['IP'] . DIRECTORY_SEPARATOR . 'images'
		: $GLOBALS['wgUploadDirectory'];

	$sTMPCacheDir   = empty( $GLOBALS['wgFileCacheDirectory'] )
		? $sTMPUploadDir . DIRECTORY_SEPARATOR . 'cache'
		: $GLOBALS['wgFileCacheDirectory'];

	$sTMPUploadPath = empty($GLOBALS['wgUploadPath']) ? $GLOBALS['wgScriptPath'] . "/images" : $GLOBALS['wgUploadPath'];

	if (!defined('BS_DATA_DIR')) {
		define( 'BS_DATA_DIR',  $sTMPUploadDir. DIRECTORY_SEPARATOR . 'bluespice' ); //Future
	}
	if (!defined('BS_CACHE_DIR')) {
		define( 'BS_CACHE_DIR', $sTMPCacheDir. DIRECTORY_SEPARATOR . 'bluespice' ); //$wgCacheDirectory?
	}
	if (!defined('BS_DATA_PATH')) {
		define('BS_DATA_PATH', $sTMPUploadPath. '/bluespice');
        }
}

if (!defined('BS_NS_OFFSET')) {
	define('BS_NS_OFFSET', 1500);
}
