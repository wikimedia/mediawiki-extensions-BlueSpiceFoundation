<?php

namespace BlueSpice\ConfigDefinition;

class Favicon extends StringSetting {

	public function getLabelMessageKey() {
		return 'bs-pref-faviconpath';
	}

	public function getVariableName() {
		return 'wg' . $this->getName();
	}

	public function isStored() {
		return true;
	}
}
