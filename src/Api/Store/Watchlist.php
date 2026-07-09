<?php

namespace BlueSpice\Api\Store;

class Watchlist extends \BlueSpice\Api\Store {

	/**
	 * @return \BlueSpice\Data\Watchlist\Store
	 */
	protected function makeDataStore() {
		return new \BlueSpice\Data\Watchlist\Store( $this->getContext(), true );
	}
}
