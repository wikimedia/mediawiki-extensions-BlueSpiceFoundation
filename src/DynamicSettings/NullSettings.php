<?php

namespace BlueSpice\DynamicSettings;

use BlueSpice\DynamicSettingsBase;

class NullSettings extends DynamicSettingsBase {

	/**
	 * @inheritDoc
	 */
	protected function doApply( &$globals ) {
		// Do nothing
	}

	/**
	 * @inheritDoc
	 */
	protected function doPersist() {
		// Do nothing
	}
}
