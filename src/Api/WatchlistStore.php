<?php

namespace BlueSpice\Api;

class WatchlistStore extends \BlueSpice\StoreApiBase {

	protected function makeDataStore() {
		return new \BlueSpice\Data\Watchlist\Store( $this->getContext() );
	}
}
