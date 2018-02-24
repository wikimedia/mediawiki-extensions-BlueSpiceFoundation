<?php

class BSUserFixturesProvider implements BSFixturesProvider {

	/**
	 * @return array[]
	 */
	public function getFixtureData() {
		$oData = FormatJson::decode( file_get_contents( __DIR__ . "/data/users.json" ) );
		return $oData->users;
	}
}
