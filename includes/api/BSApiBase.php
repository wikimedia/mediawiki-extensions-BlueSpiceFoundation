<?php
/**
 * Provides the base api for BlueSpice.
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
 * For further information visit http://bluespice.com
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
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
	 * @param User|null $oUser the User object of the requesting user. Does a fallback to $this->getUser();
	 */
	protected function checkPermissions( $aTitles = array(), $oUser = null ) {
		$aRequiredPermissions = $this->getRequiredPermissions();
		if( empty( $aRequiredPermissions ) ) {
			return; //No need for further checking
		}

		if( $oUser instanceof User === false ) {
			$oUser = $this->getUser();
		}

		$status = Status::newGood();
		foreach( $aTitles as $oTitle ) {
			if( $oTitle instanceof Title === false ) {
				continue;
			}
			foreach( $aRequiredPermissions as $sPermission ) {
				foreach ( $oTitle->getUserPermissionsErrors( $sPermission, $oUser ) as $error ) {
					$status->fatal(
						ApiMessage::create( $error, null, [ 'title' => $oTitle->getPrefixedText() ] )
					);
				}
			}
		}

		//Fallback if not conrete title was provided
		if( empty( $aTitles ) ) {
			foreach( $aRequiredPermissions as $sPermission ) {
				if( $oUser->isAllowed( $sPermission ) === false ) {
					$status->fatal(
						[ 'apierror-permissiondenied', $this->msg( "action-$sPermission" ) ]
					);
				}
			}
		}

		if ( !$status->isOK() ) {
			$this->dieStatus( $status );
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

	/**
	 * Get the Config object
	 *
	 * @since 1.23
	 * @return Config
	 */
	public function getConfig() {
		return \MediaWiki\MediaWikiServices::getInstance()
			->getConfigFactory()->makeConfig( 'bsg' );
	}
}
