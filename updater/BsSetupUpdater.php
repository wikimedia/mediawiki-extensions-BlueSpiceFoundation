<?php
define('BSBASE', dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))));
$lines = file(BSBASE."/includes/DefaultSettings.php");
foreach ($lines as $line) {
	if (  strpos( $line, 'wgVersion')){
		$aLine = explode("'", $line);
		$sVersion = $aLine[1];
		break;
	}

}
unset($_SERVER['REQUEST_METHOD']);
global $argv;
$argv = array('--quick');

if ( $sVersion >= "1.17"){
	require_once(dirname(dirname(__DIR__)) . "/maintenance/Maintenance.php");

	class BsSetupUpdater extends Maintenance{
		function execute() {
			global $wgVersion, $wgTitle, $wgLang;
			$db = wfGetDB( DB_MASTER );

			$updates = array('core','extensions');

			$updater = DatabaseUpdater::newForDb( $db, false, $this );
			$updater->doUpdates( $updates );

			foreach( $updater->getPostDatabaseUpdateMaintenance() as $maint ) {
				$child = $this->runChild( $maint );
				$child->execute();
			}
		}
	}
	$wgUseMasterForMaintenance = true;
	$maintClass = 'BsSetupUpdater';
	require_once( RUN_MAINTENANCE_IF_MAIN );
}