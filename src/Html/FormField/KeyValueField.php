<?php

namespace BlueSpice\Html\FormField;

class KeyValueField extends \HTMLTextField {

	public function getInputOOUI( $value ) {
		$attrs = array_merge( [
			'value' => $value
		], $this->mParams );
		return new \BlueSpice\Html\OOUI\KeyValueInputWidget( $attrs );
	}

}
