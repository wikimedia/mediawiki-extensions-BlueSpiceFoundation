<?php

namespace BlueSpice\ConfigDefinition;

class FileExtensions extends ArraySetting {

	public function getLabelMessageKey() {
		return 'bs-pref-imageextensions';
	}

	public function isStored() {
		return true;
	}
}
