<?php
/**
 * This class serves as a backend for the generic page store.
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
 * For further information visit http://www.blue-spice.org
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2015 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 *
 * Example request parameters of an ExtJS store
 */
class BSApiWikiPageStore extends BSApiExtJSDBTableStoreBase {

	public function makeTables( $sQuery, $aFilter ) {
		return array(
			'page'
		);
	}

	public function makeFields( $sQuery, $aFilter ) {
		return array(
			'page_id',
			'page_namespace',
			'page_title'
		);
	}

	public function postProcessData( $aData ) {
		//Before we trim, we save the count
		$this->iFinalDataSetCount = count( $aData );

		//Last, do trimming
		$aData = $this->trimData( $aData );
		return $aData;
	}

	public function makeDataSet($row) {
		if( !$oTitle = Title::newFromRow($row) ) {
			return false;
		}
		return $oTitle->userCan( 'read', $this->getUser() )
			? parent::makeDataSet( $row )
			: false
		;
	}
}