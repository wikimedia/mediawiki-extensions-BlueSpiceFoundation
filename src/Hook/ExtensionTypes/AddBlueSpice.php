<?php

namespace BlueSpice\Hook\ExtensionTypes;

class AddBlueSpice extends \BlueSpice\Hook\ExtensionTypes {

	protected function doProcess() {
		$this->extTypes['bluespice'] = \Message::newFromKey(
			"bs-exttype-bluespice"
		)->plain();
	}
}
