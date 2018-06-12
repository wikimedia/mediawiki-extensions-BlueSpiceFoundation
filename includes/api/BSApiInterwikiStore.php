<?php
/**
 * This class serves as a backend for the Interwiki store.
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
 *
 * Example request parameters of an ExtJS store
 */
class BSApiInterwikiStore extends BSApiExtJSStoreBase {
	/**
	 * @param string $sQuery Potential query provided by ExtJS component.
	 * This is some kind of preliminary filtering. Subclass has to decide if
	 * and how to process it
	 * @return array - Full list of of data objects. Filters, paging, sorting
	 * will be done by the base class
	 */
	protected function makeData( $sQuery = '' ) {
		$aInterwikiData = \MediaWiki\MediaWikiServices::getInstance()->getInterwikiLookup()->getAllPrefixes();

		$aData = array();
		foreach( $aInterwikiData as $a ) {
			$aData[] = (object) $a;
		}

		return $aData;
	}
}
