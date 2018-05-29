<?php

namespace BlueSpice\Tests;

use BlueSpice\Tests\BSFixturesProvider;

class BSPageFixturesProvider implements BSFixturesProvider {

	/**
	 * @return array[]
	 */
	public function getFixtureData() {
		$oData = \FormatJson::decode( file_get_contents( __DIR__ . "/json/pages.json" ) );
		return $oData->pages;
	}
}
