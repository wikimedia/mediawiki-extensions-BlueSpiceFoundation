<?php
/**
 * Maintenance script to modify bluespice user properties (MW 1.17.0+ only)
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
$options = [ 'help', 'execute', 'user', 'property', 'filtervalue', 'setvalue' ];
require_once 'BSMaintenance.php';
print_r( "\nMEDIAWIKI 1.17.0+ only!\n" );
print_r( $options );

$bDry = true;
if ( isset( $options['execute'] ) ) {
	$bDry = false;
}

if ( isset( $options['help'] ) ) {
	showHelp();
} else {
	if ( isset( $options['property'] ) && isset( $options['user'] ) ) {
		modifyPropertiesController( $bDry, $options );
	} else {
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

/**
 *
 * @param bool $bDry
 * @param array $options
 * @return void
 */
function modifyPropertiesController( $bDry, $options ) {
	$aUserStore = getMPCUser( $options['user'] );
	if ( empty( $aUserStore ) ) {
		echo "User does not exist!";
		return;
	}

	$aUserStore = getMPCUserValue( $aUserStore, $options['property'], $options['filtervalue'] );
	if ( empty( $aUserStore ) ) {
		echo 'property or filtervalue does not exist!';
		return;
	}

	if ( !$options['setvalue'] ) {
		displayMPCResult( $aUserStore );
		return;
	}

	updateUserProperties( $aUserStore, $options, $bDry );
}

/**
 *
 * @param array $aUserStore
 * @param array $options
 * @param bool $bDry
 */
function updateUserProperties( $aUserStore, $options, $bDry ) {
	$dbw = MediaWikiServices::getInstance()->getDBLoadBalancer()
		->getConnection( DB_PRIMARY );

	$iCounter = count( $aUserStore );
	for ( $i = 0; $i < $iCounter; $i++ ) {
		if ( $aUserStore[$i]['value'] != "null" && $aUserStore[$i]['value'] != $options['setvalue'] ) {
			if ( !$bDry ) {
				$dbw->replace(
					'user_properties',
					[ 'up_user', 'up_property' ],
					[
						'up_user' => $aUserStore[$i]['id'],
						'up_property' => $options['property'],
						'up_value' => $options['setvalue']
					],
					__METHOD__
				);
			}
			$aUserStore[$i]['setvalue'] = $options['setvalue'];

		}
		displayMPCResult( [ $aUserStore[$i] ] );
	}
	echo "Dont forget to clear memcache :)";
}

/**
 *
 * @param array $aUserStore
 */
function displayMPCResult( $aUserStore ) {
	foreach ( $aUserStore as $aUser ) {
		$sSetvalue = !empty( $aUser["setvalue"] ) ? ' => ' . $aUser["setvalue"] : '';
		echo $aUser["name"] . ": " . $aUser["value"] . $sSetvalue . "\n";
	}
}

/**
 *
 * @param array $aUserStore
 * @param string $property
 * @param bool $filtervalue
 * @return array
 */
function getMPCUserValue( $aUserStore, $property, $filtervalue = false ) {
	$dbr = MediaWikiServices::getInstance()->getDBLoadBalancer()
		->getConnection( DB_REPLICA );

	$iCounter = count( $aUserStore );
	for ( $i = 0; $i < $iCounter; $i++ ) {

		$conditions = [];
		$conditions[] = "up_user = '" . $aUserStore[$i]['id'] . "'";
		$conditions[] = "up_property = '" . $property . "'";

		if ( isset( $filtervalue ) ) {
			$conditions[] = "up_value = '" . $filtervalue . "'";
		}

		$rRes = $dbr->selectRow(
			'user_properties',
			'up_value',
			$conditions,
			__METHOD__
		);

		if ( !$rRes && $filtervalue ) {
			unset( $aUserStore[$i] );
			continue;
		}
		$aUserStore[$i]['value'] = $rRes ? $rRes->up_value : "null";
	}

	return array_values( $aUserStore );
}

/**
 *
 * @param string $sGivenUser
 * @return array
 */
function getMPCUser( $sGivenUser ) {
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
