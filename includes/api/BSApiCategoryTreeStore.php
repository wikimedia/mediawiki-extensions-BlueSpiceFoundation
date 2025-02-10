<?php

use MediaWiki\Api\ApiBase;
use MediaWiki\Category\Category;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use Wikimedia\ParamValidator\ParamValidator;

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
	 * @param string $sNode source node
	 * @return array
	 */
	private function getSubCategoriesFromPath( $sNode ) {
		$aNodes = explode( '/', $sNode );
		$sCatTitle = str_replace( '+', '/', str_replace( ' ', '_', array_pop( $aNodes ) ) );
		if ( $this->detectRecursion( $aNodes ) ) {
			return [];
		}

		$resSubCategories = $this->dbr->select(
			[ 'page', 'categorylinks' ],
			[ 'page_title' ],
			[ 'cl_to' => $sCatTitle, 'page_namespace' => NS_CATEGORY ],
			__METHOD__,
			[ '' ],
			[ 'categorylinks' =>
				[
					'INNER JOIN', 'page_id = cl_from'
				]
			]
		);

		$aSubCategories = [];

		foreach ( $resSubCategories as $row ) {
			$aSubCategories[] = $row->page_title;
		}
		asort( $aSubCategories );

		return $this->parseResult( $aSubCategories, $sNode );
	}

	/**
	 * Creates a list of categories for the source(src/) node.
	 *
	 * @return array
	 */
	private function getRootCategories() {
		$aSubCategories = [];
		$aCategories = [];

		$resSubCategories = $this->dbr->select(
			[ 'page', 'categorylinks' ],
			[ 'page_title AS cat_title' ],
			[ 'page_namespace' => NS_CATEGORY ],
			__METHOD__,
			[ '' ],
			[ 'categorylinks' =>
				[
					'INNER JOIN', 'page_id = cl_from'
				]
			]
		);

		foreach ( $resSubCategories as $row ) {
			$sCatTitle = preg_replace( '/\'/', "\'", $row->cat_title );
			$aSubCategories[] = $sCatTitle;
		}

		$resPageTable = $this->dbr->select(
			[ 'page' ],
			[ '*' ],
			[ 'page_namespace' => NS_CATEGORY, 'page_title NOT IN (\'' . implode( '\', \'', $aSubCategories ) . '\')' ],
			__METHOD__,
			[ '' ],
			[]
		);

		foreach ( $resPageTable as $row ) {
			$aCategories[] = $row->page_title;
		}

		$resCategoryTable = $this->dbr->select(
			[ 'category', 'categorylinks' ],
			'cat_title',
			[
				'cat_title NOT IN (\'' . implode( '\', \'', $aSubCategories ) . '\')',
				'cat_title = cl_to'
			],
			__METHOD__,
			[ 'GROUP BY cat_title' ]
		);

		foreach ( $resCategoryTable as $row ) {
			$aCategories[] = $row->cat_title;
		}

		$aCategories = array_unique( $aCategories );
		asort( $aCategories );
		return $this->parseResult( $aCategories, "src" );
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
