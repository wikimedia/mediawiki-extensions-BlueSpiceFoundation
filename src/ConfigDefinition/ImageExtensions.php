<?php

namespace BlueSpice\ConfigDefinition;

class ImageExtensions extends ArraySetting {

	public function getLabelMessageKey() {
		return 'bs-pref-fileextensions';
	}

	public function isStored() {
		return true;
	}

	public function isRLConfigVar() {
		return true;
	}
}
