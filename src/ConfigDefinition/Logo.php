<?php

namespace BlueSpice\ConfigDefinition;

class Logo extends StringSetting {

	public function getLabelMessageKey() {
		return 'bs-pref-logopath';
	}

	public function getVariableName() {
		return 'wg' . $this->getName();
	}

	public function isStored() {
		return true;
	}
}
