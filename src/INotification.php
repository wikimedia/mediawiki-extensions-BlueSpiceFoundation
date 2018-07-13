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
	 * @return array
	 */
	public function getAudience();

	/**
	 * @return \User The user that initiated the notification
	 */
	public function getUser();

	/**
	 * Gets configuration for secondary links
	 * if any exist
	 *
	 * @return array
	 */
	public function getSecondaryLinks();

	/**
	 * Whether mail for this notification should
	 * be sent immediately regardless of user settings
	 *
	 * @return boolean
	 */
	public function sendImmediateEmail();

	/**
	 * Whether job queue should be used
	 * to send this notification
	 *
	 * @return boolean
	 */
	public function useJobQueue();

	/**
	 *
	 * @return \Title|null
	 */
	public function getTitle();
}
