<?php

namespace BlueSpice;

interface INotification {
	/**
	 *
	 * @param string $key
	 * @param array $params
	 */
	public function __construct( $key, $params );

	/**
	 * @return string
	 */
	public function getKey();

	/**
	 * @return array
	 */
	public function getParams();

	/**
	 * @return array
	 */
	public function getAudience();

	/**
	 * @return \User The user that initiated the notification
	 */
	public function getUser();
}
