<?php

namespace BlueSpice\Html\FormField;

use BlueSpice\Html\OOUI\KeyValueInputWidget;
use HTMLTextField;

class KeyValueField extends HTMLTextField {

	/**
	 *
	 * @param string $value
	 * @return KeyValueInputWidget
	 */
	public function getInputOOUI( $value ) {
		$attrs = array_merge( [
			'value' => $value
		], $this->mParams );

		return $this->getWidget( $attrs );
	}

	/**
	 * @param array $attrs
	 * @return KeyValueInputWidget
	 */
	protected function getWidget( $attrs = [] ) {
		return new KeyValueInputWidget( $attrs );
	}

}
