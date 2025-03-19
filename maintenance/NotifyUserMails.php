<?php
/**
 * Maintenance script to notify e-mail adresses of one/all user(s)
 *
 * @file
 * @ingroup Maintenance
 * @author Patric Wirth
 * @license GPL-3.0-only
 */

use MediaWiki\MediaWikiServices;

// PW:
// TODO: use serialize
// TODO: support MW < 1.17.0
$options = [ 'help', 'execute', 'user', ];
require_once 'BSMaintenance.php';
print_r( $options );

$bDry = true;
if ( isset( $options['execute'] ) ) {
	$bDry = false;
}

if ( isset( $options['help'] ) ) {
	showHelp();
} else {
	if ( isset( $options['user'] ) ) {
		notifyUserMailController( $bDry, $options );
	} else {
		showHelp();
	}
}

function showHelp() {
	echo( "notify e-mail adresses of one/all user(s)\n" );
	echo( "Usage: php NotifyUserMails.php [<option>=<>]\n" );
	echo( " --help : displays description\n\n" );
	echo( " --user : notify e-mail adress of this user [name or id] [-1 = all users]\n" );
	echo( " --execute : Actually modify\n" );
}

/**
 *
 * @param bool $bDry
 * @param array $options
 * @return void
 */
function notifyUserMailController( $bDry, $options ) {
	$aUserStore = getUser( $options['user'] );
	if ( empty( $aUserStore ) ) {
		echo "User does not exist!";
		return;
	}
	var_dump( wfTimestamp() );
	// NotifyUser($aUserStore, $options, $bDry);
}

/**
 *
 * @param string $sGivenUser
 * @return array
 */
function getUser( $sGivenUser ) {
	if ( $sGivenUser != "-1" ) {
		if ( !ctype_digit( $sGivenUser ) ) {
			$condition = [ 'user_name = \'' . $sGivenUser . '\'' ];
		} else {
			$condition = [ 'user_id = ' . $sGivenUser ];
		}
	}

	$dbr = MediaWikiServices::getInstance()->getDBLoadBalancer()
		->getConnection( DB_REPLICA );
	$rRes = $dbr->select(
		'user',
		[ 'user_id', 'user_name' ],
		$condition,
		__METHOD__
	);

	if ( !$rRes ) {
		return [];
	}

	$aUser = [];
	foreach ( $rRes as $oRow ) {
		$aUser[] = [ 'id' => $oRow->user_id, 'name' => $oRow->user_name ];
	}

	return $aUser;
}

/**
 *
 * @param array $aUserStore
 * @param array $options
 * @param bool $bDry
 */
function NotifyUser( $aUserStore, $options, $bDry ) {
	$dbw = MediaWikiServices::getInstance()->getDBLoadBalancer()
		->getConnection( DB_PRIMARY );

	$iCounter = count( $aUserStore );
	for ( $i = 0; $i < $iCounter; $i++ ) {
		if ( !$bDry ) {

			$dbw->update(
				'user',
				[ 'user_email_authenticated' => wfTimestamp() ],
				[ 'user_id' => $aUserStore['id'] ],
				__METHOD__
			);
			/*
			$dbw->replace(
					'user',
					array( 'up_user' , 'up_property' ),
					array(
							'up_user' => $aUserStore[$i]['id'],
							'up_property' => $options['property'],
							'up_value' => $options['setvalue']
					)
			);
			 * */
		}
		$aUserStore[$i]['setvalue'] = $options['setvalue'];
		displayMPCResult( [ $aUserStore[$i] ] );
	}
}

/**
 *
 * @param array $aUserStore
 */
function displayResult( $aUserStore ) {
	foreach ( $aUserStore as $aUser ) {
		$sSetvalue = !empty( $aUser["setvalue"] ) ? ' => ' . $aUser["setvalue"] : '';
		echo $aUser["name"] . ": " . $aUser["value"] . $sSetvalue . "\n";
	}
}
