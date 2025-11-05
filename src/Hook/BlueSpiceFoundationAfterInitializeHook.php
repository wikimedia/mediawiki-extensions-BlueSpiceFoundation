<?php

namespace BlueSpice\Hook;

interface BlueSpiceFoundationAfterInitializeHook {

	/**
	 * Called after BlueSpice Foundation has initialized all its configurations
	 * @return void
	 */
	public function onBlueSpiceFoundationAfterInitialize(): void;
}
