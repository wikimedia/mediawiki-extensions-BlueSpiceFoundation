<?php

namespace BlueSpice\ConfigDefinition;

class Favicon extends StringSetting implements IOverwriteGlobal {

	/**
	 *
	 * @return string[]
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
		return 'bs-pref-faviconpath';
	}

	/**
	 *
	 * @return string
	 */
	public function getVariableName() {
		return 'wg' . $this->getName();
	}

	/**
	 *
	 * @return string
	 */
	public function getHelpMessageKey() {
		return 'bs-pref-faviconpath-help';
	}

	/**
	 * Global name to override it
	 *
	 * @return string
	 */
	public function getGlobalName() {
		return 'wgFavicon';
	}
}
