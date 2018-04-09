<?php

namespace BlueSpice;

class NotifierFactory {

	/**
	 *
	 * @return \BlueSpice\INotifier
	 */
	public static function newNotifier() {
		return new LegacyNotifier();
	}
}
