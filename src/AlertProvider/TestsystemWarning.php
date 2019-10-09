<?php

namespace BlueSpice\AlertProvider;

use BlueSpice\AlertProviderBase;
use BlueSpice\IAlertProvider;

class TestsystemWarning extends AlertProviderBase {

	/**
	 *
	 * @return string
	 */
	public function getHTML() {
		// TBD
		return '';
	}

	/**
	 *
	 * @return string
	 */
	public function getType() {
		return IAlertProvider::TYPE_WARNING;
	}

}
