<?php

namespace BlueSpice\Tests;

use MediaWiki\Json\FormatJson;

class BSUserFixturesProvider implements BSFixturesProvider {

	/**
	 * @return array[]
	 */
	public function getFixtureData() {
		$oData = FormatJson::decode( file_get_contents( __DIR__ . "/json/users.json" ) );
		return $oData->users;
	}
}
