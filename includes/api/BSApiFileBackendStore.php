<?php
/**
 *  This class serves as a backend for ExtJS stores. It allows all
 * necessary parameters and provides convenience methods and a standard ouput
 * format
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
class BSApiFileBackendStore extends BSApiExtJSStoreBase {

	public function makeData( $sQuery = '' ) {
		$res = $this->fetchCaseInsensitive( $sQuery );

		//The initial query is made against the searchindex table, which holds
		//lowercased and otherwise normalized titles. Unfornunately if
		//one queries an exact title with dots (and colons) the result will be
		//empty because the searchindex table data is stripped from those
		//characters. We will fallback to a query without the use of
		//searchindex, just in case...
		if( $res->numRows() === 0 ) {
			$res = $this->fetchCaseSensitive( $sQuery );
		}

		$bUseSecureFileStore = BsExtensionManager::isContextActive(
			'MW::SecureFileStore::Active'
		);

		//First query: Get all files and their pages
		$aReturn = array();
		$aUserNames = array();
		foreach( $res as $oRow ) {
			try {
				$oImg = RepoGroup::singleton()->getLocalRepo()
						->newFileFromRow( $oRow );
			} catch (Exception $ex) {
				continue;
			}

			$oTitle = Title::newFromRow( $oRow );
			//No "user can read" check here, because it may be expensive.
			//This may be done by hook handlers

			//TODO: use 'thumb.php'?
			//TODO: Make thumb size a parameter
			$sThumb = $oImg->createThumb( 120 );
			$sUrl = $oImg->getUrl();
			if( $bUseSecureFileStore ) { //TODO: Remove
				$sThumb = html_entity_decode( SecureFileStore::secureStuff( $sThumb, true ) );
				$sUrl = html_entity_decode( SecureFileStore::secureStuff( $sUrl, true ) );
			}

			$aUserNames[$oImg->getUser( 'text' )] = '';

			$aReturn[ $oRow->page_id ] = (object) array(
				'file_url' => $sUrl,
				'file_name' => $oImg->getName(),
				'file_size' => $oImg->getSize(),
				'file_bits' => $oImg->getBitDepth(),
				'file_user' => $oImg->getUser( 'id' ),
				'file_width' => $oImg->getWidth(),
				'file_height' => $oImg->getHeight(),
				'file_mimetype' => $oImg->getMimeType(), # major/minor
				'file_user_text' => $oImg->getUser( 'text' ),
				'file_user_display_text' => $oImg->getUser( 'text' ), //Will be overridden in a separate step
				'file_user_link' => '-',
				'file_extension' => $oImg->getExtension(),
				'file_timestamp' => $this->getLanguage()->userAdjust( $oImg->getTimestamp() ),
				'file_mediatype' => $oImg->getMediaType(),
				'file_description' => $oImg->getDescription(),
				'file_display_text' => str_replace( '_', ' ', $oImg->getName() ),
				'file_thumbnail_url' => $sThumb,
				'page_link' => '-',
				'page_id' => $oTitle->getArticleID(),
				'page_title' => $oTitle->getText(),
				'page_prefixed_text' => $oTitle->getPrefixedText(),
				'page_latest' => $oTitle->getLatestRevID(),
				'page_namespace' => $oTitle->getNamespace(),
				'page_categories' => array(), //Filled by a second step below
				'page_categories_links' => array(),
				'page_is_redirect' => $oTitle->isRedirect(),

				//For some reason 'page_is_new' and 'page_touched' are not
				//initialized by 'Title::newFromRow'; Instead when calling
				//'Title->isNew()' or 'Title->getTouched()' an extra query is
				//being sent to the database, wich introduced a performance
				//issue. As the resulting data is the same we just use the raw
				//form here.
				'page_is_new' => (bool)$oRow->page_is_new,
				'page_touched' => $oRow->page_touched
			);
		}

		//Second query: Get all categories of each file page
		$aPageIds = array_keys( $aReturn );
		if( !empty( $aPageIds ) ) {
			$oDbr = wfGetDB( DB_SLAVE );
			$oCatRes = $oDbr->select(
				'categorylinks',
				array( 'cl_from', 'cl_to' ),
				array( 'cl_from' => $aPageIds )
			);
			foreach( $oCatRes as $oCatRow ) {
				$aReturn[$oCatRow->cl_from]->page_categories[] = $oCatRow->cl_to;
			}
		}

		//Third query (for performance reasons we can not provide the full
		//link to the user page here): get user_real_name
		if( !empty( $aUserNames ) ) {
			$oDbr = wfGetDB( DB_SLAVE );
			$oUserRes = $oDbr->select(
				'user',
				array( 'user_name', 'user_real_name' ),
				array( 'user_name' => array_keys( $aUserNames ) )
			);

			foreach( $oUserRes as $oUserRow ) {
				$aUserNames[$oUserRow->user_name] = $oUserRow->user_real_name;
			}

			foreach( $aReturn as $iPageId => $oDataSet ) {
				if( !empty( $aUserNames[ $oDataSet->file_user_text ] ) ) {
					$oDataSet->file_user_display_text = $aUserNames[ $oDataSet->file_user_text ];
				}
			}
		}

		return array_values( $aReturn );
		//TODO: Find out if or where this hook was used before
		//wfRunHooks( 'BSInsertFileGetFilesBeforeQuery', array( &$aConds, &$aNameFilters ) );
	}

	public function fetchCaseInsensitive( $sQuery ) {
		$oDbr = wfGetDB( DB_SLAVE );

		$aContidions = array(
			'page_namespace' => NS_FILE,
			'page_title = img_name',
			'page_id = si_page' //Needed for case insensitive quering; Maybe
			//implement 'query' as a implicit filter on 'img_name' field?
		);

		if( !empty( $sQuery ) ) {
			$aContidions[] = "si_title ".$oDbr->buildLike(
				$oDbr->anyString(),
				strtolower( $sQuery ), //make case insensitive!
				$oDbr->anyString()
			);
		}

		$res = $oDbr->select(
			array( 'image', 'page', 'searchindex' ),
			'*',
			$aContidions,
			__METHOD__
		);

		return $res;
	}

	public function fetchCaseSensitive( $sQuery ) {
		$oDbr = wfGetDB( DB_SLAVE );

		$aContidions = array(
			'page_namespace' => NS_FILE,
			'page_title = img_name',
		);

		if( !empty( $sQuery ) ) {
			$aContidions[] = "img_name ".$oDbr->buildLike(
				$oDbr->anyString(),
				str_replace(' ', '_', $sQuery ),
				$oDbr->anyString()
			);
		}

		$res = $oDbr->select(
			array( 'image', 'page' ),
			'*',
			$aContidions,
			__METHOD__
		);

		return $res;
	}

	public function filterString($oFilter, $aDataSet) {
		$aSpecialFilterFields = [ 'page_categories', 'page_categories_links' ];
		if( !in_array( $oFilter->field, $aSpecialFilterFields ) ) {
			return parent::filterString( $oFilter, $aDataSet );
		}

		$sField = $oFilter->field;
		if( $sField === 'page_categories_links' ) {
			$sField = 'page_categories';
		}

		$sFieldValue = '';
		foreach( $aDataSet->{$sField} as $sValue ) {
			$sFieldValue .= $sValue;
		}

		return BsStringHelper::filter( $oFilter->comparison, $sFieldValue, $oFilter->value );
	}
}
