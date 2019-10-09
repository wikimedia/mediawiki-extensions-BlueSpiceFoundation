<?php

namespace BlueSpice\AlertProvider;

use BlueSpice\AlertProviderBase;
use BlueSpice\IAlertProvider;

class ReadOnlyWarning extends AlertProviderBase {

	/**
	 *
	 * @return string
	 */
	public function getHTML() {
		$readOnly = $this->getConfig()->get( 'ReadOnly' );
		if ( !$readOnly ) {
			return '';
		}

		return wfMessage( 'readonlytext', $readOnly )->parse();
	}

	/**
	 *
	 * @return string
	 */
	public function getType() {
		return IAlertProvider::TYPE_WARNING;
	}

}
