<?php

use MediaWiki\Api\ApiBase;
use MediaWiki\Category\Category;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use Wikimedia\ParamValidator\ParamValidator;
use Wikimedia\Rdbms\IDatabase;

class BSApiCategoryTreeStore extends BSApiExtJSStoreBase {

	/**
	 * @var string[]
	 */
	protected $trackingCategories = null;

	/**
	 * @var IDatabase
	 */
	private $dbr;

	/**
	 *
	 * @param string $sQuery
	 * @return array
	 */
	protected function makeData( $sQuery = '' ) {
		$sNode = $this->getParameter( 'node' );
		$this->dbr = $this->getDB();

		if ( $sNode === 'src' ) {
			return $this->getRootCategories();
		} else {
			return $this->getSubCategoriesFromPath( $sNode );
		}
	}

	/**
	 * Creates a list of subcategories for the specified node.
	 *
	 * @param string $node source node
	 * @return array
	 */
	private function getSubCategoriesFromPath( $node ) {
		$nodes = explode( '/', $node );
		$catTitle = str_replace( '+', '/', str_replace( ' ', '_', array_pop( $nodes ) ) );

		if ( $this->detectRecursion( $nodes ) ) {
			return [];
		}

		// Subcategories always have a page
		$resSubCategories = $this->dbr->select(
			[ 'page', 'categorylinks', 'linktarget' ],
			'page_title',
			[
				'lt_title' => $catTitle,
				'page_namespace' => NS_CATEGORY,
				'cl_type' => 'subcat'
			],
			__METHOD__,
			[],
			[
				'categorylinks' => [
					'INNER JOIN',
					'page_id = cl_from',
				],
				'linktarget' => [
					'INNER JOIN',
					'cl_target_id = lt_id',
				],
			]
		);

		$dubCategories = [];

		foreach ( $resSubCategories as $row ) {
			$dubCategories[] = $row->page_title;
		}

		asort( $dubCategories );

		return $this->parseResult( $dubCategories, $node );
	}

	/**
	 * Creates a list of categories for the source(src/) node.
	 *
	 * @return array
	 */
	private function getRootCategories() {
		$subCategories = [];
		$categories = [];

		$resSubCategories = $this->dbr->select(
			[ 'page', 'categorylinks' ],
			[ 'page_title AS cat_title' ],
			[ 'page_namespace' => NS_CATEGORY ],
			__METHOD__,
			[],
			[ 'categorylinks' =>
				[
					'INNER JOIN', 'page_id = cl_from'
				]
			]
		);

		foreach ( $resSubCategories as $row ) {
			$catTitle = preg_replace( '/\'/', "\'", $row->cat_title );
			$subCategories[] = $catTitle;
		}

		$resPageTable = $this->dbr->select(
			[ 'page' ],
			'*',
			[
				'page_namespace' => NS_CATEGORY,
				'page_title NOT IN (\'' . implode( '\', \'', $subCategories ) . '\')'
			],
			__METHOD__
		);

		foreach ( $resPageTable as $row ) {
			$categories[] = $row->page_title;
		}

		// Some root categories exist without a page
		$resCategoryTable = $this->dbr->select(
			[ 'category', 'categorylinks' ],
			'cat_title',
			[
				'cat_title NOT IN (\'' . implode( '\', \'', $subCategories ) . '\')'
			],
			__METHOD__,
			[ 'GROUP BY cat_title' ]
		);

		foreach ( $resCategoryTable as $row ) {
			$categories[] = $row->cat_title;
		}

		$categories = array_unique( $categories );
		asort( $categories );

		return $this->parseResult( $categories, "src" );
	}

	/**
	 * Parse the array of categories into a acceptable format for sending.
	 * For each category with subcategories, create an items array
	 *
	 * Warning: $oCategory->items calls $this->getSubCategoriesFromPath() function
	 * which can loop the request!
	 *
	 * @param array $categoriesArray
	 * @param string $sNode
	 * @return array
	 */
	public function parseResult( $categoriesArray, $sNode = 'src' ) {
		$aResult = [];
		foreach ( $categoriesArray as $sCategory ) {
			$oTmpCat = Category::newFromName( $sCategory );
			$oCategory = new stdClass();
			$oCategory->text = str_replace( '_', ' ', $oTmpCat->getName() );
			$oCategory->leaf = ( $oTmpCat->getSubcatCount() > 0 ) ? false : true;
			$oCategory->tracking = $this->isTrackingCategory( $oTmpCat );
			$oCategory->id = $sNode . '/' . str_replace( '/', '+', $oCategory->text );
			$oCategory->items =
				( $oTmpCat->getSubcatCount() > 0 )
				? $this->getSubCategoriesFromPath( $oCategory->id )
				: [];
			$aResult[] = $oCategory;
		}
		return $aResult;
	}

	/**
	 *
	 * @param array $aProcessedData
	 * @return array
	 */
	public function trimData( $aProcessedData ) {
		/**
		 * In a tree store paging is not anted in most cases
		 * TODO: Promote to dedicated BSApiExtJSTreeStoreBase class
		 */
		if ( $this->getRequest()->getInt( 'limit', -1 ) === -1 ) {
			return $aProcessedData;
		}

		return parent::trimData( $aProcessedData );
	}

	/**
	 *
	 * @return array
	 */
	public function getAllowedParams() {
		return parent::getAllowedParams() + [
			'node' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_DEFAULT => '',
				ApiBase::PARAM_HELP_MSG => 'apihelp-bs-category-treestore-param-node',
			]
		];
	}

	/**
	 *
	 * @param Category $category
	 * @return bool
	 */
	protected function isTrackingCategory( Category $category ) {
		foreach ( $this->getTrackingCategories() as $trackingCatArray ) {
			if ( !isset( $trackingCatArray['cats'] ) ) {
				continue;
			}
			foreach ( $trackingCatArray['cats'] as $title ) {
				$categoryTitle = Title::castFromPageReference( $category->getPage() );
				if ( $categoryTitle->equals( $title ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 *
	 * @return string[]
	 */
	protected function getTrackingCategories() {
		if ( $this->trackingCategories !== null ) {
			return $this->trackingCategories;
		}
		$trackingCategories = MediaWikiServices::getInstance()->getTrackingCategories();
		$this->trackingCategories = $trackingCategories->getTrackingCategories();
		return $this->trackingCategories;
	}

	/**
	 * @param array $nodes
	 *
	 * @return bool
	 */
	private function detectRecursion( array $nodes ) {
		$processed = [];
		foreach ( $nodes as $node ) {
			if ( in_array( $node, $processed ) ) {
				return true;
			}
			$processed[] = $node;
		}
		return false;
	}
}
