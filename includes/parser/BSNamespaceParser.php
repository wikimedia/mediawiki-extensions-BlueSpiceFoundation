<?php
/**
 * Turns an string into a valid Namespace ID integer
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
 * BSNamespaceParser class in BlueSpice
 * @package BlueSpice_Foundation
 */
class BSNamespaceParser extends \ValueParsers\StringValueParser {

	/**
	 *
	 * @param string $value
	 * @return int
	 */
	protected function stringParse( $value ) {
		if( is_int( $value ) ) {
			return (int)$value;
		}

		$iNSId = BsNamespaceHelper::getNamespaceIndex( $value );
		if( $iNSId === false ) {
			throw new \ValueParsers\ParseException(
				wfMessage( 'bs-parser-error-invalid-namespace' , $value )->plain()
			);
		}

		return $iNSId;
	}
}
