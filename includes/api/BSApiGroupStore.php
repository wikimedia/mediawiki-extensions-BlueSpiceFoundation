<?php
/**
 * This class serves as a backend for the group store.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit https://bluespice.com
 *
 * @author     Patric Wirth
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 *
 * Example request parameters of an ExtJS store
 */

class BSApiGroupStore extends BSApiExtJSStoreBase {

	/**
	 *
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
	 *
	 * @return string[]
	 */
	public function getRequiredPermissions() {
		return parent::getRequiredPermissions() + [
			'wikiadmin'
		];
	}

	/**
	 *
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
