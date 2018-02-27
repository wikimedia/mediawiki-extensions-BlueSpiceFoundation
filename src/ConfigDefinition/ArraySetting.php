<?php

namespace BlueSpice\ConfigDefinition;

abstract class ArraySetting extends \BlueSpice\ConfigDefinition {

	public function getHtmlFormField() {
		return new \HTMLMultiSelectEx( $this->makeFormFieldParams() );
	}

	protected function makeFormFieldParams() {
		$params = parent::makeFormFieldParams();
		$params['options'] = $this->getOptions();
		return $params;
	}

	protected function getOptions() {
		return $this->getValue();
	}
}
