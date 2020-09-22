<?php

namespace BlueSpice\Html\FormField;

use BlueSpice\Html\OOUI\KeyObjectInputWidget;

class KeyObjectField extends KeyValueField {
	/**
	 * @inheritDoc
	 */
	protected function getWidget( $attrs = [] ) {
		return new KeyObjectInputWidget( $attrs );
	}

}
