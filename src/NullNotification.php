<?php

namespace BlueSpice;

use User;

/**
 * Generic empty notification
 */
class NullNotification implements INotification {
	protected $key;

	/**
	 *
	 * @param string $key
	 * @param array $params
	 */
	public function __construct( $key, $params ) {
		$this->key = $key;
	}

	/**
	 *
	 * @return array
	 */
	public function getAudience() {
		return [];
	}

	/**
	 *
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 *
	 * @return array
	 */
	public function getParams() {
		return [];
	}

	/**
	 *
	 * @return User
	 */
	public function getUser() {
		return \BlueSpice\Services::getInstance()
			->getBSUtilityFactory()->getMaintenanceUser()->getUser();
	}

	/**
	 * Gets configuration for secondary links
	 * if any exist
	 *
	 * @return array
	 */
	public function getSecondaryLinks() {
		return [];
	}

	/**
	 * Whether mail for this notification should
	 * be sent immediately regardless of user settings
	 *
	 * @return bool
	 */
	public function sendImmediateEmail() {
		return false;
	}

	/**
	 * Whether job queue should be used
	 * to send this notification
	 *
	 * @return bool
	 */
	public function useJobQueue() {
		return false;
	}

	/**
	 *
	 * @return \Title|null
	 */
	public function getTitle() {
		return null;
	}
}
