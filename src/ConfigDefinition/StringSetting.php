<?php

namespace BlueSpice\ConfigDefinition;

abstract class StringSetting extends \BlueSpice\ConfigDefinition {

	/**
	 *
	 * @return \HTMLTextFieldOverride
	 */
	public function getHtmlFormField() {
		return new \HTMLTextFieldOverride( $this->makeFormFieldParams() );
	}

	/**
	 *
	 * @return array
	 */
	protected function makeFormFieldParams() {
		$params = parent::makeFormFieldParams();
		return $params;
	}
}
