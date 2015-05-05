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
 * For further information visit http://www.blue-spice.org
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */
class BSApiFileBackendStore extends BSApiExtJSStoreBase {

	public function makeData() {
		$oDbr = wfGetDB( DB_SLAVE );

		$aContidions = array(
			'page_namespace' => NS_FILE,
			'page_title = img_name',
			'page_id = si_page' //Needed for case insensitive quering; Maybe
			//implement 'query' as a implicit filter on 'img_name' field?
		);

		$sQuery = $this->getParameter( 'query' );
		if( !empty( $sQuery ) ) {
			$aContidions[] = "si_title ".$oDbr->buildLike(
				$oDbr->anyString(),
				$sQuery,
				$oDbr->anyString()
			);
		}

		$oImgRes = $oDbr->select(
			array( 'image', 'page', 'searchindex' ),
			'*',
			$aContidions,
			__METHOD__
		);

		$bUseSecureFileStore = BsExtensionManager::isContextActive(
			'MW::SecureFileStore::Active'
		);

		//First query: Get all files and their pages
		$aReturn = array();
		foreach( $oImgRes as $oRow ) {
			try {
				$oImg = RepoGroup::singleton()->getLocalRepo()->newFileFromRow(
					$oRow
				);
			} catch (Exception $ex) {
				continue;
			}

			$oTitle = Title::newFromRow( $oRow );

			//TODO: use 'thumb.php'?
			//TODO: Make thumb size editable
			$sThumb = $oImg->createThumb( 48, 48 );
			$sUrl = $oImg->getUrl();
			if( $bUseSecureFileStore ) {
				$sThumb = SecureFileStore::secureStuff( $sThumb, true );
				$sUrl = SecureFileStore::secureStuff( $sUrl, true );
			}

			$aReturn[ $oRow->page_id ] = (object) array(
				'file_url' => $sUrl,
				'file_name' => $oImg->getName(),
				'file_size' => $oImg->getSize(),
				'file_bits' => $oImg->getBitDepth(),
				'file_user' => $oImg->getUser( 'id' ),
				'file_width' => $oImg->getWidth(),
				'file_height' => $oImg->getHeight(),
				'file_mimetype' => $oImg->getMimeType(), # major/minor
				'file_metadata' => unserialize( $oImg->getMetadata() ),
				'file_user_text' => $oImg->getUser( 'text' ),
				'file_extension' => $oImg->getExtension(),
				'file_timestamp' => $oImg->getTimestamp(),
				'file_major_mime' => $oRow->img_major_mime,
				'file_mediatype' => $oImg->getMediaType(),
				'file_description' => $oImg->getDescription(),
				'file_display_text' => $oImg->getName(),
				'file_thumbnail_url' => $sThumb,
				'page_id' => $oTitle->getArticleID(),
				'page_title' => $oTitle->getText(),
				'page_is_new' => $oTitle->isNewPage(),
				'page_latest' => $oTitle->getLatestRevID(),
				'page_touched' => $oTitle->getTouched(),
				'page_namespace' => $oTitle->getNamespace(),
				'page_categories' => array(),
				'page_is_redirect' => $oTitle->isRedirect(),
			);
		}

		//Second query: Get all categories of each file page
		$aPageIds = array_keys( $aReturn );
		if( !empty( $aPageIds ) ) {
			$oCatRes = $oDbr->select(
				'categorylinks',
				array( 'cl_from', 'cl_to' ),
				array( 'cl_from' => $aPageIds )
			);
			foreach( $oCatRes as $oCatRow ) {
				$aReturn[$oCatRow->cl_from]->page_categories[] = $oCatRow->cl_to;
			}
		}

		return array_values( $aReturn );
		//TODO: Find out if or where this hook was used before
		//wfRunHooks( 'BSInsertFileGetFilesBeforeQuery', array( &$aConds, &$aNameFilters ) );
	}
}
