<?php

namespace BlueSpice\Special;

abstract class ManagerBase extends ExtJSBase {

	protected function getClasses() {
		return parent::getClasses() + [
				'bs-manager-container'
		];
	}

	protected function getLoadPlaceholderTemplateName() {
		return 'CRUDGrid';
	}
}
