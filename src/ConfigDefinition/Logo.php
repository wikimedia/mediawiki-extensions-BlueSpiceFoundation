<?php

namespace BlueSpice\ConfigDefinition;

class Logo extends StringSetting {

	public function getPaths() {
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_SKINNING . '/' . static::EXTENSION_FOUNDATION,
			static::MAIN_PATH_EXTENSION . '/' . static::EXTENSION_FOUNDATION . '/' . static::FEATURE_SKINNING,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_FREE . '/' . static::EXTENSION_FOUNDATION,
		];
	}

	public function getLabelMessageKey() {
		return 'bs-pref-logopath';
	}

	public function getVariableName() {
		return 'wg' . $this->getName();
	}
}
