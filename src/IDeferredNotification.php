<?php

namespace BlueSpice;

use MediaWiki\Message\Message;

interface IDeferredNotification {
	/**
	 * @return Message
	 */
	public function getMessage();

	/**
	 * @return array
	 */
	public function getOptions();
}
