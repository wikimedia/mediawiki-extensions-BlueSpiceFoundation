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
 * For further information visit https://bluespice.com
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @author     Patric Wirth
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */

use Wikimedia\ParamValidator\ParamValidator;
use Wikimedia\Rdbms\IResultWrapper;

class BSApiFileBackendStore extends BSApiExtJSStoreBase {

	/**
	 *
	 * @return array
	 */
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
				'page_categories_links' => [
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

	/**
	 *
	 * @param string $sQuery
	 * @return array
	 */
	public function makeData( $sQuery = '' ) {
		$res = $this->fetchCaseInsensitive( $sQuery );

		// The initial query is made against the searchindex table, which holds
		// lowercased and otherwise normalized titles. Unfornunately if
		// one queries an exact title with dots (and colons) the result will be
		// empty because the searchindex table data is stripped from those
		// characters. We will fallback to a query without the use of
		// searchindex, just in case...
		if ( $res->numRows() === 0 ) {
			$res = $this->fetchCaseSensitive( $sQuery );
		}

		// First query: Get all files and their pages
		$aReturn = [];
		$aUserNames = [];
		$now = MWTimestamp::now();
		$adjustedNow = $this->getLanguage()->userAdjust( $now );
		$timezoneDifference = $now - $adjustedNow;
		$localRepo = $this->services->getRepoGroup()->getLocalRepo();
		foreach ( $res as $oRow ) {
			// Add fields required for schema migration. See `\CommentStore::getCommentInternal`
			$oRow->img_cid = $oRow->img_description_id;
			$oRow->img_description_text = $oRow->comment_text;
			$oRow->img_description_data = $oRow->comment_data;

			try {
				$oImg = $localRepo->newFileFromRow( $oRow );
			} catch ( Exception $ex ) {
				continue;
			}

			// No "user can read" check here, because it may be expensive.
			// This may be done by hook handlers

			$uploaderUserId = -1;
			$uploaderUserName = '';
			$uploaderUser = $oImg->getUploader();
			if ( $uploaderUser !== null ) {
				$uploaderUserId = $uploaderUser->getId();
				$uploaderUserName = $uploaderUser->getName();
			}

			$aUserNames[$uploaderUserName] = '';

			$aReturn[ $oRow->page_id ] = (object)[
				'file_url' => self::SECONDARY_FIELD_PLACEHOLDER,
				'file_name' => $oImg->getName(),
				'file_size' => $oImg->getSize(),
				'file_bits' => $oImg->getBitDepth(),
				'file_user' => $uploaderUserId,
				'file_width' => $oImg->getWidth(),
				'file_height' => $oImg->getHeight(),
				# major/minor
				'file_mimetype' => $oImg->getMimeType(),
				'file_user_text' => $uploaderUserName,
				// Will be overridden in a separate step
				'file_user_display_text' => $uploaderUserName,
				'file_user_link' => self::SECONDARY_FIELD_PLACEHOLDER,
				'file_extension' => $oImg->getExtension(),
				'file_timestamp' => (string)( $oImg->getTimestamp() - $timezoneDifference ),
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
				// Filled by a second step below
				'page_categories' => [],
				'page_categories_links' => self::SECONDARY_FIELD_PLACEHOLDER,
				'page_is_redirect' => (bool)$oRow->page_is_redirect,

				// For some reason 'page_is_new' and 'page_touched' are not
				// initialized by 'Title::newFromRow'; Instead when calling
				// 'Title->isNew()' or 'Title->getTouched()' an extra query is
				// being sent to the database, wich introduced a performance
				// issue. As the resulting data is the same we just use the raw
				// form here.
				'page_is_new' => (bool)$oRow->page_is_new,
				'page_touched' => (string)( $oRow->page_touched - $timezoneDifference )
			];
		}

		// Second query: Get all categories of each file page
		$aPageIds = array_keys( $aReturn );
		if ( !empty( $aPageIds ) ) {
			$oDbr = wfGetDB( DB_REPLICA );
			$oCatRes = $oDbr->select(
				'categorylinks',
				[ 'cl_from', 'cl_to' ],
				[ 'cl_from' => $aPageIds ],
				__METHOD__
			);
			foreach ( $oCatRes as $oCatRow ) {
				$aReturn[$oCatRow->cl_from]->page_categories[] = $oCatRow->cl_to;
			}
		}

		// Third query (for performance reasons we can not provide the full
		// link to the user page here): get user_real_name
		if ( !empty( $aUserNames ) ) {
			$oDbr = wfGetDB( DB_REPLICA );
			$oUserRes = $oDbr->select(
				'user',
				[ 'user_name', 'user_real_name' ],
				[ 'user_name' => array_keys( $aUserNames ) ],
				__METHOD__
			);

			foreach ( $oUserRes as $oUserRow ) {
				$aUserNames[$oUserRow->user_name] = $oUserRow->user_real_name;
			}

			foreach ( $aReturn as $iPageId => $oDataSet ) {
				if ( !empty( $aUserNames[ $oDataSet->file_user_text ] ) ) {
					$oDataSet->file_user_display_text = $aUserNames[ $oDataSet->file_user_text ];
				}
			}
		}

		return array_values( $aReturn );
	}

	/**
	 *
	 * @param string $sQuery
	 * @return IResultWrapper
	 */
	protected function fetchCaseInsensitive( $sQuery ) {
		$oDbr = wfGetDB( DB_REPLICA );

		$aContidions = [
			'page_namespace' => NS_FILE,
			'page_title = img_name',
			// Needed for case insensitive quering; Maybe
			// implement 'query' as a implicit filter on 'img_name' field?
			'page_id = si_page',
			'img_description_id = comment_id'
		];

		$normalQuery = str_replace( ' ', '_', $sQuery );
		if ( !empty( $sQuery ) ) {
			$aContidions[] = "si_title " . $oDbr->buildLike(
				$oDbr->anyString(),
				// make case insensitive!
				strtolower( $normalQuery ),
				$oDbr->anyString()
			);
		}

		$res = $oDbr->select(
			[ 'image', 'page', 'searchindex', 'comment' ],
			'*',
			$aContidions,
			__METHOD__
		);

		return $res;
	}

	/**
	 *
	 * @param string $sQuery
	 * @return IResultWrapper
	 */
	protected function fetchCaseSensitive( $sQuery ) {
		$oDbr = wfGetDB( DB_REPLICA );

		$aContidions = [
			'page_namespace' => NS_FILE,
			'page_title = img_name',
			'img_description_id = comment_id'
		];

		if ( !empty( $sQuery ) ) {
			$aContidions[] = "img_name " . $oDbr->buildLike(
				$oDbr->anyString(),
				str_replace( ' ', '_', $sQuery ),
				$oDbr->anyString()
			);
		}

		$res = $oDbr->select(
			[ 'image', 'page', 'comment' ],
			'*',
			$aContidions,
			__METHOD__
		);

		return $res;
	}

	/**
	 *
	 * @param array $aTrimmedData
	 * @return array
	 */
	protected function addSecondaryFields( $aTrimmedData ) {
		$linkRenderer = $this->services->getLinkRenderer();
		$localRepo = $this->services->getRepoGroup()->getLocalRepo();
		foreach ( $aTrimmedData as $oDataSet ) {
			$oFilePage = Title::makeTitle( NS_FILE, $oDataSet->page_title );
			$oDataSet->page_link = $linkRenderer->makeLink( $oFilePage );
			$oDataSet->page_prefixed_text = $oFilePage->getPrefixedText();

			$oImg = $localRepo->newFile( $oFilePage );

			// TODO: use 'thumb.php'?
			// TODO: Make thumb size a parameter
			$sThumb = $oImg->createThumb( 80, 120 );
			$sUrl = $oImg->getUrl();

			$oDataSet->file_url = $sUrl;
			$oDataSet->file_thumbnail_url = $sThumb;

			$oUserPageTitle = Title::makeTitle( NS_USER, $oDataSet->file_user_text );
			$oDataSet->file_user_link =
				$this->oLinkRenderer->makeLink( $oUserPageTitle );

			$oDataSet->page_categories_links = [];
			foreach ( $oDataSet->page_categories as $sCategory ) {
				$oCategoryTitle = Title::makeTitle( NS_CATEGORY, $sCategory );
				$oDataSet->page_categories_links[] =
					$this->oLinkRenderer->makeLink( $oCategoryTitle );
			}
		}

		return $aTrimmedData;
	}

	/**
	 *
	 * @param \stdClass $oFilter
	 * @param \stdClass $aDataSet
	 * @return array
	 */
	public function filterString( $oFilter, $aDataSet ) {
		$aSpecialFilterFields = [ 'page_categories', 'page_categories_links', 'file_name' ];
		if ( !in_array( $oFilter->field, $aSpecialFilterFields ) ) {
			return parent::filterString( $oFilter, $aDataSet );
		}

		$sField = $oFilter->field;
		if ( $sField === 'file_name' && is_string( $oFilter->value ) ) {
			$oFilter->value = str_replace( ' ', '_', $oFilter->value );
			return parent::filterString( $oFilter, $aDataSet );
		}
		if ( $sField === 'page_categories_links' ) {
			$sField = 'page_categories';
		}

		$sFieldValue = '';
		foreach ( $aDataSet->{$sField} as $sValue ) {
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

		$aParams['sort'][ParamValidator::PARAM_DEFAULT] = FormatJson::encode( [
			[
				'property' => 'file_timestamp',
				'direction' => 'DESC'
			]
		] );

		return $aParams;
	}
}
