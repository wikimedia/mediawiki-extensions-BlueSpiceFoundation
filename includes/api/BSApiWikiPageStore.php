<?php
/**
 * This class serves as a backend for the generic page store.
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
 */

use BlueSpice\Services;

class BSApiWikiPageStore extends BSApiExtJSDBTableStoreBase {

	/**
	 * This method does a preliminary filtering of the data according to
	 * the query.
	 * @param string $sQuery
	 * @return array a set of data items
	 */
	protected function makeData( $sQuery = '' ) {
		$aData = parent::makeData( $sQuery );

		// Bypass quickfilter when there is no query
		if ( $sQuery == '' ) {
			return $aData;
		}

		// Split query text into namespace and title part
		$oTitle = Title::newFromText( $sQuery );
		$sTitleText = $oTitle->getText();
		$sNamespace = $oTitle->getNamespace();

		$aNewData = [];
		foreach ( $aData as $oDataItem ) {
			// Filter namespace
			if ( $oDataItem->page_namespace != $sNamespace ) {
				continue;
			}
			// Filter title text
			if ( stripos( $oDataItem->page_title, $sTitleText ) === false ) {
				continue;
			}
			$aNewData[] = $oDataItem;
		}

		return $aNewData;
	}

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
		$aProcessedData = $this->trimData( $aData );

		//Add secondary fields
		$aProcessedData = $this->addSecondaryFields( $aProcessedData );

		return $aProcessedData;
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

	protected function addSecondaryFields( $aTrimmedData ) {
		$oLinkRenderer = Services::getInstance()->getLinkRenderer();
		foreach( $aTrimmedData as &$oDataSet ) {
			$oTitle = Title::makeTitle(
				$oDataSet->page_namespace,
				$oDataSet->page_title
			);

			$oDataSet->prefixedText = $oTitle->getPrefixedText();
			$oDataSet->displayText = $oTitle->getSubpageText();
			$oDataSet->type = 'wikipage';

			$oDataSet->page_link = $oLinkRenderer->makeLink( $oTitle );
		}

		return $aTrimmedData;
	}
}
