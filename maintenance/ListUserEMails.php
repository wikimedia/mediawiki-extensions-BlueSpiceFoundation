<?php
/**
 * Maintenance script to list e-mail addresses of all users
 *
 * @file
 * @ingroup Maintenance
 * @author Marc Reymann
 * @license GNU General Public Licence 3.0
 */

require_once( 'BSMaintenance.php' );

class ListUserEmails extends BSMaintenance {

	public function __construct() {
		parent::__construct();

		$this->addOption( 'delimiter', 'Delimiter used to concatenate addresses', false, true, true );
		$this->addOption( 'confirmed', 'Show only confirmed addresses', false, false, true );
	}
	
	public function execute() {
		$bConfirmed = (bool)$this->getOption( 'confirmed', false );
		$sDelimiter = $this->getOption( 'delimiter', "\n" );

		$this->output( "Fetching data" );
		$aAllUserData = $this->getUserData();
		if ( !$aAllUserData ) {
			$this->output( "No users found" );
			return;
		}
		$aUserMails = array();
		foreach( $aAllUserData as $aUserData ) {
			if ( $bConfirmed && $aUserData['auth'] === null ) continue;
			if ( $aUserData['email'] ) {
				$aUserMails[] = $aUserData['email'];
			}
		}
		$this->output( count($aUserMails)." users found\n" );
		$this->output( implode( $sDelimiter, $aUserMails ) );
		return;
	}
	
	private function getUserData() {
		$oDbr = wfGetDB( DB_REPLICA );
		$rRes = $oDbr->select( 
			'user',
			array(
				'user_email',
				'user_email_authenticated'
			)
		);
		
		if( !$rRes ) return array();
		
		$aUser = array();
		while( $aRow = $oDbr->fetchRow( $rRes ) ) {
			$aUser[] = array(
				'email' => $aRow['user_email'],
				'auth'  => $aRow['user_email_authenticated']
			);
		}
		
		return $aUser;
	}
}

$maintClass = 'ListUserEmails';
require_once RUN_MAINTENANCE_IF_MAIN;
