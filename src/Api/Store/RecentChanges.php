<?php

namespace BlueSpice\Api\Store;

class RecentChanges extends \BlueSpice\Api\Store {

	protected function makeDataStore() {
		return new \BlueSpice\Data\RecentChanges\Store( $this->getContext() );
	}
}
