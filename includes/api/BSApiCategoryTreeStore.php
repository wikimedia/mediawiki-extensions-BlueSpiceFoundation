<?php

class BSApiCategoryTreeStore extends BSApiExtJSStoreBase {
	protected function makeData( $sQuery = '' ) {
		$sNode = $this->getParameter( 'node' );
		$aResult = [];
		$dbr = $this->getDB();

		if ( $sNode === 'src' ) {
			$aCategories = [];
			$aSubCategories = [];

			$resSubcats = $dbr->select(
				[ 'page', 'categorylinks' ],
				[ 'page_title AS cat_title' ],
				[ 'page_namespace' => NS_CATEGORY ],
				__METHOD__,
				[ 'ORDER BY page_title' ],
				[ 'categorylinks' =>
					[
						'INNER JOIN', 'page_id = cl_from'
					]
				]
			);

			foreach ( $resSubcats as $row ) {
				$sCatTitle = preg_replace( '/\'/', "\'", $row->cat_title );
				$aSubCategories[] = $sCatTitle;
			}

			$aTables = [ 'page' ];
			$aFields = [ '*' ];
			$aConditions = [ 'page_namespace' => NS_CATEGORY ];
			$sMethod = __METHOD__;
			$aOptions = [ '' ];
			$aJoinConds = [];

			$aConditions[] = 'page_title NOT IN (\'' . implode( '\', \'', $aSubCategories ) . '\')';

			$resPageTable = $dbr->select(
				$aTables,
				$aFields,
				$aConditions,
				$sMethod,
				$aOptions,
				$aJoinConds
			);

			foreach ( $resPageTable as $row ) {
				$aCategories[] = $row->page_title;
			}

			$resCategoryTable = $dbr->select(
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

			foreach ( $aCategories as $sCategory ) {
				$oTmpCat = Category::newFromName( $sCategory );
				if ( $oTmpCat instanceof Category ) {
    }
				$oCategory = new stdClass();
				$oCategory->text = str_replace( '_', ' ', $oTmpCat->getName() );
				$oCategory->leaf = ( $oTmpCat->getSubcatCount() > 0 ) ? false : true;
				$oCategory->id = 'src/' . str_replace( '/', '+', $oCategory->text );
				$aResult[] = $oCategory;
			}
		} else {
			$aNodes = explode( '/', $sNode );
			$sCatTitle = str_replace( '+', '/', str_replace( ' ', '_', array_pop( $aNodes ) ) );

			$aTables = [ 'page', 'categorylinks' ];
			$aFields = [ 'page_title' ];
			$aConditions = [ 'cl_to' => $sCatTitle, 'page_namespace' => NS_CATEGORY ];
			$sMethod = __METHOD__;
			$aOptions = [ '' ];
			$aJoinConds = [ 'categorylinks' => [ 'INNER JOIN', 'page_id=cl_from' ] ];

			$resSubCategories = $dbr->select(
				$aTables,
				$aFields,
				$aConditions,
				$sMethod,
				$aOptions,
				$aJoinConds
			);

			$aSubCategories = [];

			foreach ( $resSubCategories as $row ) {
				$aSubCategories[] = $row->page_title;
			}
			asort( $aSubCategories );

			foreach ( $aSubCategories as $sCategory ) {
				$oTmpCat = Category::newFromName( $sCategory );
				$oCategory = new stdClass();
				$oCategory->text = str_replace( '_', ' ', $oTmpCat->getName() );
				$oCategory->leaf = ( $oTmpCat->getSubcatCount() > 0 ) ? false : true;
				$oCategory->id = $sNode . '/' . str_replace( '/', '+', $oCategory->text );
				$aResult[] = $oCategory;
			}
		}

		return $aResult;
	}

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

	public function getAllowedParams() {
		return parent::getAllowedParams() + [
			'node' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_DFLT => '',
				ApiBase::PARAM_HELP_MSG => 'apihelp-bs-category-treestore-param-node',
			]
		];
	}
}
