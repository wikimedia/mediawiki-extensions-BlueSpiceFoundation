<?php
/**
 * Maintenance script to delete old tomcat logs
 *
 * @file
 * @ingroup Maintenance
 * @author Patric Wirth
 * @license GPL-3.0-only
 */

$options = [ 'help', 'logdir', 'execute' ];
require_once 'BSMaintenance.php';
print_r( "need to have permissions on log directory\n" );
print_r( $options );

$bDry = true;
if ( isset( $options['execute'] ) ) {
	$bDry = false;
}

if ( isset( $options['help'] ) ) {
	showHelp();
} else {
	if ( isset( $options['logdir'] ) ) {
		deleteOldTomcatLogs( $bDry, $options );
	} else {
		showHelp();
	}
}

function showHelp() {
	echo( "delete tomcatlogs\n" );
	echo( "Usage: php DeleteOldTomcatLogs.php [<option>=<>]\n" );
	echo( " --help : displays description\n\n" );
	echo( " --dir : log directory [\"C:\\xampp\\tomcat\\logs\"] \n" );
	echo( " --execute : realy delete logs \n\n" );
}

/**
 *
 * @param bool $bDry
 * @param array $options
 */
function deleteOldTomcatLogs( $bDry, $options ) {
	if ( $handle = opendir( $options['logdir'] ) ) {
		while ( ( $file = readdir( $handle ) ) !== false ) {
			if ( !empty( $file )
				&& preg_match( '/.(\d{4})\-(\d{2})\-(\d{2}).log/', $file, $hits )
				&& $hits
				&& $hits[1] . $hits[2] . $hits[3] != date( 'Ymd' )
			) {
				if ( $bDry == false ) {
					unlink( $options['logdir'] . '\\' . $file );
				}
				echo ( 'deleted: ' . $file . " \n" );
			}
		}
		closedir( $handle );
	} else {
		echo 'Directory not found! ' . $options['logdir'];
	}
}
