<?php

namespace BlueSpice;

/**
 * Generic no-op notifier
 */
class NullNotifier implements \BlueSpice\INotifier {
	public function init() {
	}

	/**
	 *
	 * @param INotification $notification
	 * @return \Status
	 */
	public function notify( $notification ) {
		return \Status::newGood();
	}

	public function registerNotification( $key, $params ) {
	}

	public function unRegisterNotification( $key ) {
	}

	public function getNotificationObject( $key, $params ) {
		return new \BlueSpice\NullNotification( $key, $params );
	}

}
