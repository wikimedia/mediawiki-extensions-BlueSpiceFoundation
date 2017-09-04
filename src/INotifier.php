<?php

namespace BlueSpice;

interface INotifier {

	/**
	 *
	 * @param INotification $notification
	 */
	public function notify( $notification );
}