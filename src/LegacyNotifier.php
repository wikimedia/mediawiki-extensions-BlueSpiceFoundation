<?php

namespace BlueSpice;

class LegacyNotifier implements INotifier {


	/**
	 *
	 * @param INotification $notification
	 * @return \Status
	 */
	public function notify( $notification ) {
		\BSNotificationHandler::notify(
			$notification->getKey(),
			$notification->getUser(),
			$notification->getTitle(),
			$notification->getParams()
		);

		return \Status::newGood();
	}
}
