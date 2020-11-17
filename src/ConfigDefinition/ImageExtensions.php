<?php

namespace BlueSpice\ConfigDefinition;

class ImageExtensions extends ArraySetting {

	/**
	 *
	 * @return string
	 */
	public function getLabelMessageKey() {
		return 'bs-pref-imageextensions';
	}

	/**
	 *
	 * @return bool
	 */
	public function isRLConfigVar() {
		return true;
	}

	/**
	 *
	 * @return \HTMLMultiSelectPlusAdd
	 */
	public function getHtmlFormField() {
		return new \HTMLMultiSelectPlusAdd( $this->makeFormFieldParams() );
	}

	/**
	 *
	 * @return string|null
	 */
	public function getHelpMessageKey() {
		return 'bs-pref-imageextensions-help';
	}
}
