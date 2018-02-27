<?php

namespace BlueSpice\ConfigDefinition;

class ImageExtensions extends ArraySetting {

	public function getLabelMessageKey() {
		return 'bs-pref-imageextensions';
	}

	public function isRLConfigVar() {
		return true;
	}

	public function getHtmlFormField() {
		return new \HTMLMultiSelectPlusAdd( $this->makeFormFieldParams() );
	}
}
