<?php

class BSPageFixturesProvider {

	/**
	 * @return array[]
	 */
	public function getFixtureData() {
		$oData = FormatJson::decode( file_get_contents( __DIR__."/data/pages.json" ) );
		return $oData->pages;
	}
}