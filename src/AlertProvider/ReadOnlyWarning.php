<?php

namespace BlueSpice\AlertProvider;

use BlueSpice\AlertProviderBase;
use BlueSpice\IAlertProvider;

class ReadOnlyWarning extends AlertProviderBase {

	public function getHTML() {
		$readOnly = $this->getConfig()->get( 'ReadOnly' );
		if( !$readOnly ) {
			return '';
		}

		return wfMessage( 'readonlytext', $readOnly )->parse();
	}

	public function getType() {
		return IAlertProvider::TYPE_WARNING;
	}

}
