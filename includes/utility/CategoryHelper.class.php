<?php

class BsCategoryHelper {

	/**
	 * Returns an array of category objects
	 * @return array
	 */
	public static function getCategories() {
		$aReturn = array ();

		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
			array ( 'category' ),
			array ( 'cat_id' )
		);

		if ( !$res ) {
			return $aReturn ;
		}
		while ( $row = $res->fetchObject() ) {
			$oCategory = Category::newFromID( $row->cat_id );
			if ( is_null( $oCategory ) ) {
				continue;
			}
			$aReturn [] = $oCategory;
		}
		return $aReturn;
	}

}
