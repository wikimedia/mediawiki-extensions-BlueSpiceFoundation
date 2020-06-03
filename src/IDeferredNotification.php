<?php

namespace BlueSpice;

interface IDeferredNotification {
	/**
	 * @return \Message
	 */
	public function getMessage();

	/**
	 * @return array
	 */
	public function getOptions();
}
