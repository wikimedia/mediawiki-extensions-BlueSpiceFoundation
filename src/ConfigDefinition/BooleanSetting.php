<?php

namespace BlueSpice\ConfigDefinition;

abstract class BooleanSetting extends \BlueSpice\ConfigDefinition {

	public function getHtmlFormField() {
		return new \HTMLCheckFieldOverride( $this->makeFormFieldParams() );
	}

	protected function makeFormFieldParams() {
		$params = parent::makeFormFieldParams();
		return $params;
	}
}
