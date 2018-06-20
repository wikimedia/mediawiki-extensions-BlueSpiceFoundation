<?php
/**
 *  This class serves as a backend for ExtJS stores. It allows all
 * necessary parameters and provides convenience methods and a standard ouput
 * format
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

use BlueSpice\Services;

class BSApiFileBackendStore extends BSApiExtJSStoreBase {

	protected function makeMetaData() {
		return [
			'properties' => [
				'file_url' => [
					self::PROP_SPEC_SORTABLE => false,
					self::PROP_SPEC_FILTERABLE => false
				],
				'file_name' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_size' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_bits' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_user' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_width' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_height' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_mimetype' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_user_text' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_user_display_text' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_user_link' => [
					self::PROP_SPEC_SORTABLE => false,
					self::PROP_SPEC_FILTERABLE => false
				],
				'file_extension' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_timestamp' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_mediatype' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_description' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_display_text' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'file_thumbnail_url' => [
					self::PROP_SPEC_SORTABLE => false,
					self::PROP_SPEC_FILTERABLE => false
				],
				'page_link' => [
					self::PROP_SPEC_SORTABLE => false,
					self::PROP_SPEC_FILTERABLE => false
				],
				'page_id' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'page_title' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'page_prefixed_text' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'page_latest' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'page_namespace' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'page_categories' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'page_categories_links'=> [
					self::PROP_SPEC_SORTABLE => false,
					self::PROP_SPEC_FILTERABLE => false
				],
				'page_is_redirect' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'page_is_new' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				],
				'page_touched' => [
					self::PROP_SPEC_SORTABLE => true,
					self::PROP_SPEC_FILTERABLE => true
				]
			]
		];
	}

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

		//First query: Get all files and their pages
		$aReturn = array();
		$aUserNames = array();
		foreach( $res as $oRow ) {
			try {
				$title = Title::makeTitle( NS_FILE, $oRow->img_name );
				$oImg = RepoGroup::singleton()->getLocalRepo()
					->newFile( $title );
			} catch (Exception $ex) {
				continue;
			}

			//No "user can read" check here, because it may be expensive.
			//This may be done by hook handlers

			$aUserNames[$oImg->getUser( 'text' )] = '';

			$aReturn[ $oRow->page_id ] = (object) array(
				'file_url' => self::SECONDARY_FIELD_PLACEHOLDER,
				'file_name' => $oImg->getName(),
				'file_size' => $oImg->getSize(),
				'file_bits' => $oImg->getBitDepth(),
				'file_user' => $oImg->getUser( 'id' ),
				'file_width' => $oImg->getWidth(),
				'file_height' => $oImg->getHeight(),
				'file_mimetype' => $oImg->getMimeType(), # major/minor
				'file_user_text' => $oImg->getUser( 'text' ),
				'file_user_display_text' => $oImg->getUser( 'text' ), //Will be overridden in a separate step
				'file_user_link' => self::SECONDARY_FIELD_PLACEHOLDER,
				'file_extension' => $oImg->getExtension(),
				'file_timestamp' => $this->getLanguage()->userAdjust( $oImg->getTimestamp() ),
				'file_mediatype' => $oImg->getMediaType(),
				'file_description' => $oImg->getDescription(),
				'file_display_text' => str_replace( '_', ' ', $oImg->getName() ),
				'file_thumbnail_url' => self::SECONDARY_FIELD_PLACEHOLDER,
				'page_link' => self::SECONDARY_FIELD_PLACEHOLDER,
				'page_id' => (int)$oRow->page_id,
				'page_title' => $oRow->page_title,
				'page_prefixed_text' => self::SECONDARY_FIELD_PLACEHOLDER,
				'page_latest' => (int)$oRow->page_latest,
				'page_namespace' => (int)$oRow->page_namespace,
				'page_categories' => array(), //Filled by a second step below
				'page_categories_links' => self::SECONDARY_FIELD_PLACEHOLDER,
				'page_is_redirect' => (bool)$oRow->page_is_redirect,

				//For some reason 'page_is_new' and 'page_touched' are not
				//initialized by 'Title::newFromRow'; Instead when calling
				//'Title->isNew()' or 'Title->getTouched()' an extra query is
				//being sent to the database, wich introduced a performance
				//issue. As the resulting data is the same we just use the raw
				//form here.
				'page_is_new' => (bool)$oRow->page_is_new,
				'page_touched' => $this->getLanguage()->userAdjust( $oRow->page_touched )
			);
		}

		//Second query: Get all categories of each file page
		$aPageIds = array_keys( $aReturn );
		if( !empty( $aPageIds ) ) {
			$oDbr = wfGetDB( DB_REPLICA );
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
			$oDbr = wfGetDB( DB_REPLICA );
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
	}

	protected function fetchCaseInsensitive( $sQuery ) {
		$oDbr = wfGetDB( DB_REPLICA );

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

	protected function fetchCaseSensitive( $sQuery ) {
		$oDbr = wfGetDB( DB_REPLICA );

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

	protected function addSecondaryFields( $aTrimmedData ) {
		$linkRenderer = Services::getInstance()->getLinkRenderer();
		foreach( $aTrimmedData as $oDataSet ) {
			$oFilePage = Title::makeTitle( NS_FILE, $oDataSet->page_title );
			$oDataSet->page_link = $linkRenderer->makeLink( $oFilePage );
			$oDataSet->page_prefixed_text = $oFilePage->getPrefixedText();

			$oImg = RepoGroup::singleton()->getLocalRepo()->newFile( $oFilePage );

			//TODO: use 'thumb.php'?
			//TODO: Make thumb size a parameter
			$sThumb = $oImg->createThumb( 80, 120 );
			$sUrl = $oImg->getUrl();

			$oDataSet->file_url = $sUrl;
			$oDataSet->file_thumbnail_url = $sThumb;

			$oUserPageTitle = Title::makeTitle( NS_USER, $oDataSet->file_user_text );
			$oDataSet->file_user_link =
				$this->oLinkRenderer->makeLink(  $oUserPageTitle );

			$oDataSet->page_categories_links = [];
			foreach( $oDataSet->page_categories as $sCategory ) {
				$oCategoryTitle = Title::makeTitle( NS_CATEGORY, $sCategory );
				$oDataSet->page_categories_links[] =
					$this->oLinkRenderer->makeLink( $oCategoryTitle );
			}
		}

		return $aTrimmedData;
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

	/**
	 * Adds special default sorting
	 * @return array
	 */
	public function getAllowedParams() {
		$aParams = parent::getAllowedParams();

		$aParams['sort'][ApiBase::PARAM_DFLT] = FormatJson::encode( [
			[
				'property' => 'file_timestamp',
				'direction' => 'DESC'
			]
		] );

		return $aParams;
	}
}
