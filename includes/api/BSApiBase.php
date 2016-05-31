<?php
/**
 * Provides the base api for BlueSpice.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
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
 * This file is part of BlueSpice for MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * Api base class in BlueSpice
 * @package BlueSpice_Foundation
 */
abstract class BSApiBase extends ApiBase {
	/**
	 * Checks access permissions based on a list of titles and permissions. If
	 * one of it fails the API processing is ended with an appropriate message
	 * @param array $aTitles Array of Title objects to check the requires permissions against
	 * @param User $oUser the User object of the requesting user. Does a fallback to $this->getUser();
	 */
	protected function checkPermissions( $aTitles = array(), $oUser = null ) {
		$aRequiredPermissions = $this->getRequiredPermissions();
		if( empty( $aRequiredPermissions ) ) {
			return; //No need for further checking
		}
		foreach( $aTitles as $oTitle ) {
			if( $oTitle instanceof Title === false ) {
				continue;
			}
			foreach( $aRequiredPermissions as $sPermission ) {
				if( $oTitle->userCan( $sPermission ) === false ) {
					//TODO: Reflect title and permission in error message
					$this->dieUsageMsg( 'badaccess-groups' );
				}
			}
		}

		//Fallback if not conrete title was provided
		if( empty( $aTitles ) ) {
			if( $oUser instanceof User === false ) {
				$oUser = $this->getUser();
			}
			foreach( $aRequiredPermissions as $sPermission ) {
				if( $oUser->isAllowed( $sPermission ) === false ) {
					//TODO: Reflect permission in error message
					$this->dieUsageMsg( 'badaccess-groups' );
				}
			}
		}
	}

	protected function getRequiredPermissions() {
		return array( 'read' );
	}

	protected function getExamples() {
		return array(
			'api.php?action='.$this->getModuleName(),
		);
	}

	/**
	 * Custom output printer for JSON. See class BSApiFormatJson for details
	 * @return BSApiFormatJson
	 */
	public function getCustomPrinter() {
		return new BSApiFormatJson( $this->getMain(), $this->getParameter( 'format' ) );
	}
}