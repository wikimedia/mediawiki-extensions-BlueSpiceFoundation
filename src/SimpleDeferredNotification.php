<?php

namespace BlueSpice;

use MediaWiki\Message\Message;
use MWException;

class SimpleDeferredNotification implements IDeferredNotification {

	private $message = null;

	private $options = null;

	/**
	 * Must be an array with the following keys:
	 * - 'message': a Message object
	 * - 'options': an array of options compatible to
	 * https://doc.wikimedia.org/mediawiki-core/master/js/#!/api/mw.notification-property-defaults
	 * @param array $notificationInfo
	 */
	public function __construct( $notificationInfo ) {
		if ( !isset( $notificationInfo['message'] ) ) {
			throw new MWException( "Key 'message' must be set!" );
		}

		$this->message = $notificationInfo['message'];
		$this->options = isset( $notificationInfo['options'] )
			? $notificationInfo['options']
			: [];
	}

	/**
	 *
	 * @return Message
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 *
	 * @return array
	 */
	public function getOptions() {
		return $this->options;
	}
}
