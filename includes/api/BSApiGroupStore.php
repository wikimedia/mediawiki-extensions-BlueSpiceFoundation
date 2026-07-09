<?php

class BSApiGroupStore extends BSApiExtJSStoreBase {

	/**
	 * @var string
	 */
	protected $sLcQuery = '';

	/**
	 * @param string $sQuery
	 * @return array - List of of groups
	 */
	protected function makeData( $sQuery = '' ) {
		if ( $sQuery === null ) {
			$sQuery = '';
		}
		$this->sLcQuery = strtolower( $sQuery );

		$aData = [];
		$groupHelper = $this->services->getService( 'BSUtilityFactory' )->getGroupHelper();
		$explicitGroups = $groupHelper->getAvailableGroups( [ 'filter' => [ 'explicit' ] ] );
		foreach ( $explicitGroups as $sGroup ) {
			$sDisplayName = $sGroup;
			$oMsg = wfMessage( "group-$sGroup" );
			if ( $oMsg->exists() ) {
				$sDisplayName = $oMsg->text() . " ($sGroup)";
			}

			if ( !$this->queryApplies( $sGroup, $sDisplayName ) ) {
				continue;
			}

			$aData[] = (object)[
				'group_name' => $sGroup,
				'additional_group' => ( $groupHelper->getGroupType( $sGroup ) == 'custom' ),
				'group_type' => $groupHelper->getGroupType( $sGroup ),
				'displayname' => $sDisplayName,
			];
		}
		return $aData;
	}

	/**
	 * @return string[]
	 */
	public function getRequiredPermissions() {
		return parent::getRequiredPermissions() + [
			'wikiadmin'
		];
	}

	/**
	 * @param string $sGroup
	 * @param string $sDisplayName
	 * @return bool
	 */
	protected function queryApplies( $sGroup, $sDisplayName ) {
		if ( empty( $this->sLcQuery ) ) {
			return true;
		}

		$sLcGroup = strtolower( $sGroup );
		$sLcDisplayname = strtolower( $sDisplayName );

		return strpos( $sLcGroup, $this->sLcQuery ) !== false
			|| strpos( $sLcDisplayname, $this->sLcQuery ) !== false;
	}
}
