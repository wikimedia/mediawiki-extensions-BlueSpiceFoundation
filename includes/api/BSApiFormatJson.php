<?php
/**
 * Provides the api json format for BlueSpice.
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
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */

/**
 * Api json formal class in BlueSpice
 * @package BlueSpice_Foundation
 */
class BSApiFormatJson extends ApiFormatJson {
	public function getAllowedParams() {
		$aParams = parent::getAllowedParams();
		if( isset( $aParams['formatversion'] ) ) {

			/*
			 * This is needed for most ExtJS frontent components.
			 * New MediaWiki versions normalize JSON output in a way that
			 * fields with boolean true are converted to fields with empty
			 * string value and fields with boolean false just get removed from
			 * the JSON string. This breaks some ExtJS logic (mainly store
			 * implementations)
			 * Changing the 'formatversion' to 2, disables this behavior
			 */
			$aParams['formatversion'][ApiBase::PARAM_DFLT] = 2;
		}

		return $aParams;
	}
}
