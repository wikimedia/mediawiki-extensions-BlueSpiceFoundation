<?php

namespace BlueSpice\Special;

abstract class ManagerBase extends ExtJSBase {

	/**
	 *
	 * @return string
	 */
	protected function getClasses() {
		return parent::getClasses() + [
				'bs-manager-container'
		];
	}

	/**
	 *
	 * @return string
	 */
	protected function getLoadPlaceholderTemplateName() {
		return 'CRUDGrid';
	}
}
