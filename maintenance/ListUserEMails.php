<?php
/**
 * Maintenance script to list e-mail addresses of all users
 *
 * @file
 * @ingroup Maintenance
 * @author Marc Reymann
 * @licence GNU General Public Licence 2.0 or later
 */

require_once( 'BSMaintenance.php' );

class ListUserEmails extends Maintenance {

	public function __construct() {
		parent::__construct();
	
		$this->addOption( 'delimiter', 'Delimiter used to concatenate addresses', false, true );
		$this->addOption( 'confirmed', 'Show only confirmed addresses', false, false );
	}
	
	public function execute() {
		$bConfirmed = $this->getOption( 'confirmed' );
		$sDelimiter = $this->getOption( 'delimiter' );
		if ( !$sDelimiter ) $sDelimiter = "\n";
		$aAllUserData = $this->getUserData();
		if ( !$aAllUserData ) {
			echo "No users found";
			return;
		}
		$aUserMails = array();
		foreach( $aAllUserData as $aUserData ) {
			if ( $bConfirmed && $aUserData['auth'] === NULL ) continue;
			if ( $aUserData['email'] ) $aUserMails[] = $aUserData['email'];
		}
		echo join( $sDelimiter, $aUserMails );
		return;
	}
	
	private function getUserData() {
		$oDbr = wfGetDB( DB_SLAVE );
		$rRes = $oDbr->select( 'user', array('user_email','user_email_authenticated') );
		
		if( !$rRes ) return array();
		
		$aUser = array();
		while( $aRow = $oDbr->fetchRow( $rRes ) ) {
			$aUser[] = array(       'email' => $aRow['user_email'],
						'auth'  => $aRow['user_email_authenticated']
					);
		}
		
		return $aUser;
	}
}

$maintClass = 'ListUserEmails';
if (defined('RUN_MAINTENANCE_IF_MAIN')) {
	require_once( RUN_MAINTENANCE_IF_MAIN );
} else {
	require_once( DO_MAINTENANCE ); # Make this work on versions before 1.17
}
