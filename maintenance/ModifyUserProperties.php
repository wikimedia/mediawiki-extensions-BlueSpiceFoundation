<?php
/**
 * Maintenance script to modify bluespice user properties (MW 1.17.0+ only)
 *
 * @file
 * @ingroup Maintenance
 * @author Patric Wirth
 * @license GNU General Public Licence 3.0
 */

//PW:
//TODO: use serialize
//TODO: support MW < 1.17.0
$options = array( 'help', 'execute', 'user', 'property', 'filtervalue', 'setvalue' );
require_once( 'BSMaintenance.php' );
print_r( "\nMEDIAWIKI 1.17.0+ only!\n" );
print_r( $options );

$bDry = true;
if( isset( $options['execute'] ) ) { 
	$bDry = false;
}

if( isset( $options['help'] ) ) {
	showHelp();
}
else {
	if( isset( $options['property'] ) && isset( $options['user'] ) ) {
		modifyPropertiesController( $bDry, $options );
	}
	else {
		showHelp();
	}
}

function showHelp() {
	echo( "modify user properties\n" );
	echo( "Usage: php ModifyUserProperties.php [<option>=<>]\n" );
	echo( " --help : displays description\n\n" );
	echo( " --user : modify property of this user [name or id] [-1 = all users]\n" );
	echo( " --property : property to modify\n" );
	echo( " --execute : Actually modify\n" );
	echo( " --filtervalue : filter by value\n" );
	echo( " --setvalue : set value of given property\n\n" );
}

function modifyPropertiesController( $bDry, $options ) {
	$aUserStore = getMPCUser( $options['user'] );
	if( empty( $aUserStore ) ) {
		echo "User does not exist!";
		return;
	}
	
	$aUserStore = getMPCUserValue( $aUserStore, $options['property'], $options['filtervalue'] );
	if( empty($aUserStore) ) {
		echo 'property or filtervalue does not exist!';
		return;
	}
	
	if( !$options['setvalue'] ) {
		displayMPCResult($aUserStore);
		return;
	}
	
	updateUserProperties($aUserStore, $options, $bDry);
}

function updateUserProperties($aUserStore, $options, $bDry) {
	$oDbw = wfGetDB( DB_MASTER );
	
	$iCounter = count($aUserStore);
	for($i = 0; $i < $iCounter; $i++) {
		if( $aUserStore[$i]['value'] != "null" && $aUserStore[$i]['value'] != $options['setvalue']) {
			if( !$bDry ) {
				$oDbw->replace( 
						'user_properties', 
						array( 'up_user' , 'up_property' ),
						array( 
								'up_user' => $aUserStore[$i]['id'],
								'up_property' => $options['property'],
								'up_value' => $options['setvalue']
						) 
				); 
			}
			$aUserStore[$i]['setvalue'] = $options['setvalue'];
			
		}
		displayMPCResult(array($aUserStore[$i]));
	}
	echo "Dont forget to clear memcache :)";
	
}

function displayMPCResult( $aUserStore ) {
	foreach( $aUserStore as $aUser) {
			$sSetvalue = !empty( $aUser["setvalue"]) ? ' => '.$aUser["setvalue"] : '';
			echo $aUser["name"].": ".$aUser["value"].$sSetvalue."\n";
		}
}
function getMPCUserValue( $aUserStore, $property, $filtervalue=false ) {
	
	$oDbr = wfGetDB( DB_REPLICA );
	
	$iCounter = count($aUserStore);
	for($i = 0; $i < $iCounter; $i++) {
		
		$conditions = array();
		$conditions[] = "up_user = '".$aUserStore[$i]['id']."'";
		$conditions[] = "up_property = '".$property."'";
		
		if( isset( $filtervalue ) ) {
			$conditions[] = "up_value = '".$filtervalue."'";
		}
		
		$rRes = $oDbr->selectRow( 
			'user_properties',
			'up_value', 
			$conditions
		);
		
		if( !$rRes && $filtervalue) {
			unset( $aUserStore[$i] );
			continue;
		}
		$aUserStore[$i]['value'] = $rRes ? $rRes->up_value : "null";
	}
	
	return array_values($aUserStore);
}

function getMPCUser( $sGivenUser ) {
	
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
			array('user_id','user_name'), 
			$condition 
	);
	
	if( !$rRes ) return array();
	
	$aUser = array();
	while( $oRow = $oDbr->fetchRow( $rRes ) ) {
		$aUser[] = array( 'id' => $oRow['user_id'], 
						'name' => $oRow['user_name'] 
						);
	}
	
	return $aUser;
}
