<?php

namespace BlueSpice\ConfigDefinition;

class FileExtensions extends ArraySetting {

	public function getLabelMessageKey() {
		return 'bs-pref-fileextensions';
	}

	public function isRLConfigVar() {
		return true;
	}

	public function getHtmlFormField() {
		return new \HTMLMultiSelectPlusAdd( $this->makeFormFieldParams() );
	}
}
