<?php

class BSApiUserStore extends BSApiExtJSStoreBase {

	protected function makeData($sQuery = '') {
		$dbr = $this->getDB();

		$aGroups = array();
		$groupsRes = $dbr->select( 'user_groups', '*' );
		foreach( $groupsRes as $row ) {
			if( !isset( $aGroups[$row->ug_user] ) ) {
				$aGroups[$row->ug_user] = array();
			}
			$aGroups[$row->ug_user][] = $row->ug_group;
		}

		//TODO: It would be very cool to have the permissions as a filterable
		//field. Unfortunately this requires some context information from the
		//client. I.e. The page/namespace for which the permissions should be
		//calculated. This would also be very expensive and a potential
		//security issue.

		$aData = array();
		$userRes = $dbr->select( 'user', '*' );
		foreach( $userRes as $aRow ) {
			$aResRow = $this->makeResultRow( $aRow, $aGroups );
			if( !$aResRow ) {
				continue;
			}
			$aData[] = (object)$aResRow;
		}

		return $aData;
	}

	/**
	 * Builds a single result set
	 * @param stdClass $row
	 * @param array $aGroups
	 * @return array
	 */
	protected function makeResultRow( $row, $aGroups = array() ) {
		$oUserPageTitle = Title::makeTitle( NS_USER, $row->user_name );
		return array(
			'user_id' => $row->user_id,
			'user_name' => $row->user_name,
			'user_real_name' => $row->user_real_name,
			'user_registration' => $row->user_registration,
			'user_editcount' => $row->user_editcount,
			'groups' => isset( $aGroups[$row->user_id] ) ? $aGroups[$row->user_id] : array(),
			'page_link' => Linker::link( $oUserPageTitle, $row->user_name.' ' ), //The whitespace is to aviod automatic rewrite to user_real_name by BSF

			//legacy fields
			'display_name' => $row->user_real_name == null ? $row->user_name : $row->user_real_name,
			'page_prefixed_text' => $oUserPageTitle->getPrefixedText()
		);
	}
}