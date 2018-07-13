<?php

namespace BlueSpice;

use BlueSpice\INotification;
/**
 * Generic empty notification
 */
class NullNotification implements INotification {
	protected $key;

	public function __construct( $key, $params ) {
		$this->key = $key;
	}

	public function getAudience() {
		return [];
	}

	public function getKey() {
		return $this->key;
	}

	public function getParams() {
		return [];
	}

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
	 * @return boolean
	 */
	public function sendImmediateEmail() {
		return false;
	}

	/**
	 * Whether job queue should be used
	 * to send this notification
	 *
	 * @return boolean
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
