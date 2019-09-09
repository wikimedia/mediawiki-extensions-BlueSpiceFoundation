<?php

namespace BlueSpice\ConfigDefinition;

abstract class IntSetting extends \BlueSpice\ConfigDefinition {

	/**
	 *
	 * @return \HTMLIntFieldOverride
	 */
	public function getHtmlFormField() {
		return new \HTMLIntFieldOverride( $this->makeFormFieldParams() );
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
