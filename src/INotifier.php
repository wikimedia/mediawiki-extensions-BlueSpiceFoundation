<?php

namespace BlueSpice;

interface INotifier {

	/**
	 * Use this to run whatever is necessary
	 * to set up the notifier
	 */
	public function init();

	/**
	 * @param string $key
	 * @param array| null $params
	 * @return mixed
	 */
	public function registerNotificationCategory( $key, $params );

	/**
	 *
	 * @param string $key
	 * @param array $params
	 */
	public function registerNotification( $key, $params );

	/**
	 *
	 * @param string $key
	 */
	public function unRegisterNotification( $key );

	/**
	 *
	 * @param INotification $notification
	 */
	public function notify( $notification );

	/**
	 *
	 * @param string $key
	 * @param array $params
	 */
	public function getNotificationObject( $key, $params );
}
