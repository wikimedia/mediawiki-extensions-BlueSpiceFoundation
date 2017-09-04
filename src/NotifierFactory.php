<?php

namespace BlueSpice;

class NotifierFactory {

	/**
	 *
	 * @return \BlueSpice\INotifier
	 */
	public function newNotifier() {
		return new LegacyNotifier();
	}
}
