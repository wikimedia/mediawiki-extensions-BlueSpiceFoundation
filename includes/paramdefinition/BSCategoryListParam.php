<?php
/**
 * @package BlueSpice_Foundation
 */
class BSCategoryListParam extends \ParamProcessor\ParamDefinition {

	/** @var string */
	protected $delimiter = '|';
	/** @var BSTitleValidator|null */
	protected $validator = null;

	protected function postConstruct() {
		$this->validator = new BSTitleValidator();
	}

	/**
	 * @return bool
	 */
	public function isList(): bool {
		return true;
	}
}
