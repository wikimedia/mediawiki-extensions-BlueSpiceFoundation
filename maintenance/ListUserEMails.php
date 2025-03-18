<?php
/**
 * Maintenance script to list e-mail addresses of all users
 *
 * @file
 * @ingroup Maintenance
 * @author Marc Reymann
 * @license GPL-3.0-only
 */

require_once 'BSMaintenance.php';

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
		$aUserMails = [];
		foreach ( $aAllUserData as $aUserData ) {
			if ( $bConfirmed && $aUserData['auth'] === null ) {
				continue;
			}
			if ( $aUserData['email'] ) {
				$aUserMails[] = $aUserData['email'];
			}
		}
		$this->output( count( $aUserMails ) . " users found\n" );
		$this->output( implode( $sDelimiter, $aUserMails ) );
	}

	private function getUserData() {
		$dbr = $this->getDB( DB_REPLICA );
		$rRes = $dbr->select(
			'user',
			[
				'user_email',
				'user_email_authenticated'
			],
			'',
			__METHOD__
		);

		if ( !$rRes ) {
			return [];
		}

		$aUser = [];
		foreach ( $rRes as $aRow ) {
			$aUser[] = [
				'email' => $aRow->user_email,
				'auth'  => $aRow->user_email_authenticated
			];
		}

		return $aUser;
	}
}

$maintClass = ListUserEmails::class;
require_once RUN_MAINTENANCE_IF_MAIN;
