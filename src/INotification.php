<?php

namespace BlueSpice;

interface INotification {
	/**
	 * @return string
	 */
	public function getKey();

	/**
	 * @return array
	 */
	public function getParams();

	/**
	 * @return \Title|null The title the notification is about. May be null.
	 */
	public function getTitle();

	/**
	 * @return \User The user that initiated the notification
	 */
	public function getUser();
}
