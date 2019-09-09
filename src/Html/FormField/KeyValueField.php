<?php

namespace BlueSpice\Html\FormField;

class KeyValueField extends \HTMLTextField {

	/**
	 *
	 * @param string $value
	 * @return \BlueSpice\Html\OOUI\KeyValueInputWidget
	 */
	public function getInputOOUI( $value ) {
		$attrs = array_merge( [
			'value' => $value
		], $this->mParams );
		return new \BlueSpice\Html\OOUI\KeyValueInputWidget( $attrs );
	}

}
