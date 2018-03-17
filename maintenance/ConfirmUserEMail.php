<?php
/**
 * Maintenance script to confirm e-mail adresses of one/all user(s)
 *
 * @file
 * @ingroup Maintenance
 * @author Patric Wirth
 * @license GNU General Public Licence 3.0
 */

require_once( 'BSMaintenance.php' );

class ConfirmUserEMail extends Maintenance {

	public function __construct() {
		parent::__construct();
	
		$this->addOption('user', 'confirm e-mail adress of this user [name or id] [-1 = all users]', true, true);
		$this->addOption('force', 'confirm user without e-mail', false, false);
		$this->addOption('execute', 'execute modify', false, false);
		$this->requireExtension( 'BlueSpiceFoundation' );
	}
	
	public function execute() {
		
		$siUser = $this->getOption( 'user' );
		$bExecute = (bool)$this->getOption( 'execute', false );
		$bForce = (bool)$this->getOption( 'force', false );
		
		$aUserStore = $this->getUser( $siUser, $bForce );
		if( empty( $aUserStore ) ) {
			$this->output( "User does not exist!" );
			return;
		}
		$aUserStore = $this->confirmUser($aUserStore, $bExecute, $bForce);
		
		$this->displayResult($aUserStore);
	}
	
	private function getUser( $sGivenUser ) {
		if( $sGivenUser != "-1") {
			if( !ctype_digit( $sGivenUser ) ) {
				$condition = array( 'user_name = \''.$sGivenUser.'\'' );
			}
			else {
				$condition = array( 'user_id = '.$sGivenUser );
			}
		}
		
		$oDbr = wfGetDB( DB_REPLICA );
		$rRes = $oDbr->select( 
				'user',
				array('user_id','user_name','user_email','user_email_authenticated'),
				$condition 
		);
		
		if( !$rRes ) return array();
		
		$aUser = array();
		while( $aRow = $oDbr->fetchRow( $rRes ) ) {
			$aUser[] = array(
				'id'	=> $aRow['user_id'],
				'name'	=> $aRow['user_name'],
				'email' => $aRow['user_email'],
				'setvalue' => $aRow['user_email_authenticated'],
				'value'	=> 'authentification'
			);
		}
		
		return $aUser;
	}
	
	private function confirmUser($aUserStore, $bExecute = false, $bForce = false) {
		$oDbw = wfGetDB( DB_MASTER );
		
		$iCounter = count($aUserStore);
		for($i = 0; $i < $iCounter; $i++) {
			if(!$bForce) {
				if( empty($aUserStore[$i]['email']) || !strpos($aUserStore[$i]['email'],'@') ) {
					$aUserStore[$i]['setvalue'] = ' ERROR: E-Mail not valid';
					continue;
				}
			}
			if( empty($aUserStore[$i]['setvalue']) ) {
				if( $bExecute ) {
					$oDbw->update( 'user',
						array('user_email_authenticated' => date('YmdHis')),
						array('user_id' => $aUserStore[$i]['id'])
					);
				}
				$aUserStore[$i]['setvalue'] = ' => confirmed';
			}
			else $aUserStore[$i]['setvalue'] = '';
		}

		return $aUserStore;
	}
	
	private function displayResult( $aUserStore ) {
		foreach( $aUserStore as $aUser) {
			$this->output( $aUser["name"].": ".$aUser["value"].$aUser["setvalue"]."\n" );
		}
	}
	
}

$maintClass = 'ConfirmUserEMail';
require_once RUN_MAINTENANCE_IF_MAIN;
