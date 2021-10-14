<?php

namespace BlueSpice\ConfigDefinition;

class Logo extends StringSetting implements IOverwriteGlobal {

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
	public function getHelpMessageKey() {
		return 'bs-pref-logopath-help';
	}

	/**
	 *
	 * @return string
	 */
	public function getVariableName() {
		return 'wg' . $this->getName();
	}

	/**
	 * Global name to override it
	 *
	 * @return string
	 */
	public function getGlobalName() {
		return 'wgLogo';
	}
}
