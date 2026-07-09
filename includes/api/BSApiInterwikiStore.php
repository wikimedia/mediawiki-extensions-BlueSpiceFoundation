<?php

class BSApiInterwikiStore extends BSApiExtJSStoreBase {
	/**
	 * @param string $sQuery Potential query provided by ExtJS component.
	 * This is some kind of preliminary filtering. Subclass has to decide if
	 * and how to process it
	 * @return array - Full list of of data objects. Filters, paging, sorting
	 * will be done by the base class
	 */
	protected function makeData( $sQuery = '' ) {
		$aInterwikiData = $this->services->getInterwikiLookup()->getAllPrefixes();

		$aData = [];
		foreach ( $aInterwikiData as $a ) {
			$aData[] = (object)$a;
		}
		$this->services->getHookContainer()->run( 'BSApiInterwikiStoreMakeData', [
			$this,
			&$aData
		] );
		return $aData;
	}
}
