<?php

use MediaWiki\Title\Title;

/**
 * This class serves as a backend for the category store.
 *
 * @deprecated since 1.43; use commonwebapis component; will be removed in 1.47
 */
class BSApiCategoryStore extends BSApiExtJSStoreBase {

	/**
	 *
	 * @param string $sQuery
	 * @return array
	 */
	protected function makeData( $sQuery = '' ) {
		// $sOptions will be used... maybe
		$oResult = new stdClass();

		$aCategories = [];
		$dbr = $this->services->getDBLoadBalancer()->getConnection( DB_REPLICA );
		// category table also tracks all deleted categories. So we need to double
		// check with categorylinks and page table. Use case for this is a category
		// name that had a spelling mistake.
		// From category table:
		// -- Track all existing categories.  Something is a category if 1) it has an en-
		// -- try somewhere in categorylinks, or 2) it once did.  Categories might not
		// -- have corresponding pages, so they need to be tracked separately.

		// (31.01.14) STM: Query had to be separated into two quieres because it was to expensive

		$res = $dbr->select(
			[ 'category', 'categorylinks' ],
			[ 'cat_id', 'cat_title', 'cat_pages', 'cat_subcats', 'cat_files' ],
			[ 'cat_title = cl_to' ],
			__METHOD__,
			[ 'DISTINCT' ]
		);

		foreach ( $res as $row ) {
			$oCategoryTitle = Title::newFromText( $row->cat_title, NS_CATEGORY );
			if ( !is_object( $oCategoryTitle ) ) {
				continue;
			}

			$oCategoryData = new stdClass();

			$oCategoryData->cat_id = (int)$row->cat_id;
			$oCategoryData->cat_title = $row->cat_title;
			$oCategoryData->text = $row->cat_title;
			$oCategoryData->cat_pages = (int)$row->cat_pages;
			$oCategoryData->cat_subcats = (int)$row->cat_subcats;
			$oCategoryData->cat_files = (int)$row->cat_files;

			$oCategoryData->prefixed_text = $oCategoryTitle->getPrefixedText();

			$aCategories[$row->cat_title] = $oCategoryData;
		}

		$res = $dbr->select(
			[ 'category', 'page' ],
			[ 'cat_id', 'cat_title', 'cat_pages', 'cat_subcats', 'cat_files' ],
			[ 'cat_title = page_title AND page_namespace = ' . NS_CATEGORY ],
			__METHOD__
		);

		foreach ( $res as $row ) {
			$oCategoryTitle = Title::newFromText( $row->cat_title, NS_CATEGORY );
			if ( !is_object( $oCategoryTitle ) ) {
				continue;
			}

			$oCategoryData = new stdClass();

			$oCategoryData->cat_id = (int)$row->cat_id;
			$oCategoryData->cat_title = $row->cat_title;
			$oCategoryData->text = $row->cat_title;
			$oCategoryData->cat_pages = (int)$row->cat_pages;
			$oCategoryData->cat_subcats = (int)$row->cat_subcats;
			$oCategoryData->cat_files = (int)$row->cat_files;

			$oCategoryData->prefixed_text = $oCategoryTitle->getPrefixedText();

			$aCategories[$row->cat_title] = $oCategoryData;
		}

		ksort( $aCategories );
		$aCategories = array_values( $aCategories );
		$aCategories = array_filter( $aCategories, [ $this, 'filterCategoriesCallback' ] );

		return $aCategories;
	}

	/**
	 * @param string $categoryData
	 * @return bool
	 */
	protected function filterCategoriesCallback( $categoryData ) {
		$query = $this->getParameter( 'query' );
		$query = $query ? trim( $query ) : null;
		if ( empty( $query ) || !is_string( $query ) ) {
			return true;
		}

		$doesContain = BsStringHelper::filter(
			BsStringHelper::FILTER_CONTAINS,
			$categoryData->cat_title,
			$query
		);

		return $doesContain;
	}
}
