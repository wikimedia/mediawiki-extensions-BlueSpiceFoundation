<?php

namespace BlueSpice\ConfigDefinition;

class FileExtensions extends ArraySetting implements IOverwriteGlobal {

	/**
	 *
	 * @return string
	 */
	public function getLabelMessageKey() {
		return 'bs-pref-fileextensions';
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
		return 'bs-pref-fileextensions-help';
	}

	/**
	 *
	 * @return string
	 */
	public function getGlobalName() {
		return "wg{$this->getName()}";
	}

}
