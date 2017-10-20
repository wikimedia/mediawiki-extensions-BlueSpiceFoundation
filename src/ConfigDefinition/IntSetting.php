<?php

namespace BlueSpice\ConfigDefinition;

class IntSetting extends \BlueSpice\ConfigDefinition {

	public function getHtmlFormField() {
		return new \HTMLIntFieldOverride( $this->makeFormFieldParams() );
	}

	protected function makeFormFieldParams() {
		$params = parent::makeFormFieldParams();
		return $params;
	}

	public function getLabelMessageKey() {
		return $this->getVariableName();
	}
}
