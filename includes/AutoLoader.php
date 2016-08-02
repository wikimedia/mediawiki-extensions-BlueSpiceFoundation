<?php

//TODO: Remove this after BsExtensionManager::registerExtension is not in use
//anymore
$GLOBALS['wgAutoloadClasses']['BsPARAM'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['BsPARAMTYPE'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['BsPARAMOPTION'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['BsPATHTYPE'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['BsRUNLEVEL'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['BsACTION'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['BsSTYLEMEDIA'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['EXTINFO'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['EXTTYPE'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['BsExtensionManager'] = __DIR__."/ExtensionManager.class.php";

//TODO: Remove this
if ( version_compare( $wgVersion, '1.19.0', '>' ) ) {
	$GLOBALS['wgAutoloadClasses']['DatabaseOracle'] = __DIR__."/db/DatabaseOraclePost120.php";
	$GLOBALS['wgAutoloadClasses']['OracleUpdater']  = __DIR__."/db/OracleUpdater.php";
} else {
	$GLOBALS['wgAutoloadClasses']['DatabaseOracle'] = __DIR__."/db/DatabaseOraclePre120.php";
}
