<?php

namespace BlueSpice\ConfigDefinition;

abstract class ArraySetting extends \BlueSpice\ConfigDefinition {

	/**
	 *
	 * @return \HTMLMultiSelectEx
	 */
	public function getHtmlFormField() {
		return new \HTMLMultiSelectEx( $this->makeFormFieldParams() );
	}

	/**
	 *
	 * @return array
	 */
	protected function makeFormFieldParams() {
		$params = parent::makeFormFieldParams();
		$params['options'] = $this->getOptions();
		return $params;
	}

	/**
	 *
	 * @return array
	 */
	protected function getOptions() {
		return $this->getValue();
	}
}
