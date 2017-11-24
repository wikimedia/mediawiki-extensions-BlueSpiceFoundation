<?php
/**
 * Provides the admin user store api for BlueSpice.
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
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */

/**
 * Api base class for admin user store in BlueSpice
 * @package BlueSpice_Foundation
 */
class BSApiAdminUserStore extends BSApiUserStore {

	protected function getRequiredPermissions() {
		return array( 'wikiadmin' );
	}

	protected function makeResultRow( $row, $aGroups = array() ) {
		$aResult = parent::makeResultRow( $row, $aGroups );
		$aResult['user_email'] = isset( $row->user_email )
			? $row->user_email
			: ''
		;
		return $aResult;
	}
}
