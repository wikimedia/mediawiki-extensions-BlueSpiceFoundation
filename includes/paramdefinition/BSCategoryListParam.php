<?php
/**
 * @package BlueSpice_Foundation
 */
class BSCategoryListParam extends \ParamProcessor\ParamDefinition {
	protected $delimiter = '|';
	protected $validator = null;

	protected function postConstruct() {
		$this->validator = new BSTitleValidator();
	}

	public function isList() {
		return true;
	}
}
