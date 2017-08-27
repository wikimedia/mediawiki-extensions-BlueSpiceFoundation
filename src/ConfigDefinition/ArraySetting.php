<?php

namespace BlueSpice\ConfigDefinition;

class ArraySetting extends \BlueSpice\ConfigDefinition {

	public function getHtmlFormField() {
		return new \HTMLMultiSelectEx( $this->makeFormFieldParams() );
	}

	protected function makeFormFieldParams() {
		$params = parent::makeFormFieldParams();
		$params['options'] = $this->getValue();
		return $params;
	}

	public function getLabelMessageKey() {
		return $this->getVariableName();
	}
}
