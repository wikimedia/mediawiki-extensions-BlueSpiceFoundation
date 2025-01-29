<?php

namespace BlueSpice\Tests;

use MediaWiki\Json\FormatJson;

class BSPageFixturesProvider implements BSFixturesProvider {

	/**
	 * @return array[]
	 */
	public function getFixtureData() {
		$oData = FormatJson::decode( file_get_contents( __DIR__ . "/json/pages.json" ) );
		return $oData->pages;
	}
}
