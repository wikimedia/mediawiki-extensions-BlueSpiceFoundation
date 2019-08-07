<?php

namespace BlueSpice\ConfigDefinition;

class Favicon extends StringSetting {

	/**
	 *
	 * @return string[]
	 */
	public function getPaths() {
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_SKINNING . '/' . static::EXTENSION_FOUNDATION,
			static::MAIN_PATH_EXTENSION . '/' . static::EXTENSION_FOUNDATION . '/' . static::FEATURE_SKINNING,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_FREE . '/' . static::EXTENSION_FOUNDATION,
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
}
