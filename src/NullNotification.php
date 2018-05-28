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

}
