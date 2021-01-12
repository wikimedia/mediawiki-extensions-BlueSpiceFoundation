<?php

namespace BlueSpice\Api\Store;

use BlueSpice\Data\Categorylinks\Store;

class Categorylinks extends \BlueSpice\Api\Store {

	protected function makeDataStore() {
		return new Store( $this->getContext(), true );
	}
}
