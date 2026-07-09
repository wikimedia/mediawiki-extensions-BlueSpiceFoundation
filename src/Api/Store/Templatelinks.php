<?php

namespace BlueSpice\Api\Store;

use BlueSpice\Data\Templatelinks\Store;

class Templatelinks extends \BlueSpice\Api\Store {

	/**
	 * @return Store
	 */
	protected function makeDataStore() {
		return new Store( $this->getContext(), true );
	}
}
