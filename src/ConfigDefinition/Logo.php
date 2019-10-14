<?php

namespace BlueSpice\ConfigDefinition;

class Logo extends StringSetting {

	/**
	 *
	 * @return array
	 */
	public function getPaths() {
		$feature = static::FEATURE_SKINNING;
		$ext = static::EXTENSION_FOUNDATION;
		$package = static::PACKAGE_FREE;
		return [
			static::MAIN_PATH_FEATURE . "/$feature/$ext",
			static::MAIN_PATH_EXTENSION . "/$ext/$feature",
			static::MAIN_PATH_PACKAGE . "/$package/$ext",
		];
	}

	/**
	 *
	 * @return string
	 */
	public function getLabelMessageKey() {
		return 'bs-pref-logopath';
	}

	/**
	 *
	 * @return string
	 */
	public function getVariableName() {
		return 'wg' . $this->getName();
	}
}
