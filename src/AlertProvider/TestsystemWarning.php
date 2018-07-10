<?php

namespace BlueSpice\AlertProvider;

use BlueSpice\AlertProviderBase;
use BlueSpice\IAlertProvider;

class TestsystemWarning extends AlertProviderBase {

	public function getHTML() {
		//TBD
		return '';
	}

	public function getType() {
		return IAlertProvider::TYPE_WARNING;
	}

}
