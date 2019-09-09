<?php

namespace BlueSpice\ConfigDefinition;

abstract class BooleanSetting extends \BlueSpice\ConfigDefinition {

	/**
	 *
	 * @return \HTMLCheckFieldOverride
	 */
	public function getHtmlFormField() {
		return new \HTMLCheckFieldOverride( $this->makeFormFieldParams() );
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
