<?php

namespace BlueSpice;

/**
 * Generic empty notification
 */
class NullNotification implements BlueSpice\INotification {
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

}
