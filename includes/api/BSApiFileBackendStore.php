<?php

class BSApiFileBackendStore extends BSApiExtJSStoreBase {

	public function makeData() {
		$oDbr = wfGetDB( DB_SLAVE );

		$aContidions = array(
			'page_namespace' => NS_FILE,
			'page_title = img_name',
			'page_id = si_page' //Needed for case insensitive quering; Maybe implement 'query' as a implicit filter on 'img_name' field?
		);

		$sQuery = $this->getParameter( 'query' );
		if( !empty($sQuery) ) {
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

		$bUseSecureFileStore = BsExtensionManager::isContextActive( 'MW::SecureFileStore::Active' );

		//First query: Get all files and their pages
		$aReturn = array();
		foreach( $oImgRes as $oRow ) {
			try {
				$oImg = RepoGroup::singleton()->getLocalRepo()->newFileFromRow( $oRow );
			} catch (Exception $ex) {
				continue;
			}

			//TODO: use 'thumb.php'?
			$sThumb = $oImg->createThumb( 48, 48 );
			if( $bUseSecureFileStore ) {
				$sThumb = SecureFileStore::secureStuff( $sThumb, true );
			}
			$oRow->img_metadata = unserialize( $oRow->img_metadata );
			$oRow->img_thumbnail = $sThumb;
			$oRow->categories = array();

			$aReturn[ $oRow->page_id ] = $oRow;
		}

		//Second query: Get all categories of each file page
		$aPageIds = array_keys( $aReturn );
		$oCatRes = $oDbr->select(
			'categorylinks',
			array( 'cl_from', 'cl_to' ),
			array( 'cl_from' => $aPageIds )
		);
		foreach( $oCatRes as $oCatRow ) {
			$aReturn[$oCatRow->cl_from]->categories[] = $oCatRow->cl_to;
		}

		return array_values( $aReturn );
		//wfRunHooks( 'BSInsertFileGetFilesBeforeQuery', array( &$aConds, &$aNameFilters ) );
	}

	public function filterCallback( $aDataSet ) {
		$aFilter = $this->getParameter('filter');
		foreach( $aFilter as $oFilter ) {
			if( $oFilter->type != 'categories' ) {
				continue;
			}
			if( !$this->filterCategories($oFilter, $aDataSet) ) {
				return false;
			}
		}
		return parent::filterCallback($aDataSet);
	}

	public function filterCategories($oFilter, $aDataSet) {
		$aFieldValue = $aDataSet->{$oFilter->field};
		$aFilterValue = $oFilter->value;

		switch( $oFilter->comparison ) {
			case 'ct':
				foreach($aFilterValue as $sValue) {
					if( in_array($sValue, $aFieldValue) ) {
						continue;
					}
					return false;
				}
				return true;
			case 'nct':
				foreach($aFilterValue as $sValue) {
					if( !in_array($sValue, $aFieldValue) ) {
						continue;
					}
					return false;
				}
				return true;
		}
	}
}
