<?php

class BSExportUsers extends BSMaintenance {
	public function execute() {
		$oDOM = new DOMDocument();
		$oDOM->formatOutput = true;
		$oUsersNode = $oDOM->createElement( 'users' );
		$oDOM->appendChild( $oUsersNode );

		$dbr = $this->getDB( DB_REPLICA );
		$res = $dbr->select( 'user', '*' );

		foreach( $res as $row ) {
			$oUserNode = $oDOM->createElement( 'user' );
			$oUsersNode->appendChild( $oUserNode );

			$oUserNode->appendChild( $oDOM->createElement( 'name', $row->user_name ) );
			$oUserNode->appendChild( $oDOM->createElement( 'id', $row->user_id ) );
			$oUserNode->appendChild( $oDOM->createElement( 'realname', $row->user_real_name ) );
			$oUserNode->appendChild( $oDOM->createElement( 'email', $row->user_email ) );
			$oUserNode->appendChild( $oDOM->createElement( 'touched', wfTimestamp( TS_ISO_8601, $row->user_touched ) ) );
			$oUserNode->appendChild( $oDOM->createElement( 'registration', wfTimestamp( TS_ISO_8601, $row->user_registration ) ) );
			$oUserNode->appendChild( $oDOM->createElement( 'editcount', $row->user_editcount ) );

			$res2 = $dbr->select( 'user_groups', '*', array( 'ug_user' => $row->user_id ) );
			if( $dbr->numRows( $res2 ) > 0 ) {
				$oGroupsNode = $oDOM->createElement( 'groups' );
				$oUserNode->appendChild( $oGroupsNode );
				foreach( $res2 as $row2 ) {
					$oGroupNode = $oDOM->createElement( 'group' );
					$oGroupNode->setAttribute( 'name', $row2->ug_group );
					$oGroupsNode->appendChild( $oGroupNode );
				}
			}

			$res3 = $dbr->select( 'user_properties', '*', array( 'up_user' => $row->user_id ) );
			if( $dbr->numRows( $res3 ) > 0 ) {
				$oPropertiesNode = $oDOM->createElement( 'properties' );
				$oUserNode->appendChild( $oPropertiesNode );
				foreach( $res3 as $row3 ) {
					$oPropertyNode = $oDOM->createElement( 'property' );
					$oPropertyNode->setAttribute( 'name', $row3->up_property );
					$oPropertyNode->setAttribute( 'value', $row3->up_value );
					$oPropertiesNode->appendChild( $oPropertyNode );
				}
			}
		}

		$this->output( $oDOM->saveXML() );
	}

}

$maintClass = 'BSExportUsers';
require_once RUN_MAINTENANCE_IF_MAIN;
