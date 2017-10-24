<?php

namespace BlueSpice\ConfigDefinition;

abstract class StringSetting extends \BlueSpice\ConfigDefinition {

	public function getHtmlFormField() {
		return new \HTMLTextFieldOverride( $this->makeFormFieldParams() );
	}

	protected function makeFormFieldParams() {
		$params = parent::makeFormFieldParams();
		return $params;
	}
}
