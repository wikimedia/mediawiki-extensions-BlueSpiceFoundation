<?php

namespace BlueSpice\Tests;

class BSUserFixturesProvider implements BSFixturesProvider {

	/**
	 * @return array[]
	 */
	public function getFixtureData() {
		$oData = \FormatJson::decode( file_get_contents( __DIR__ . "/json/users.json" ) );
		return $oData->users;
	}
}
