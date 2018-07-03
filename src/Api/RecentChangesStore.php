<?php

namespace BlueSpice\Api;

class RecentChangesStore extends \BlueSpice\StoreApiBase {

	protected function makeDataStore() {
		return new \BlueSpice\Data\RecentChanges\Store( $this->getContext() );
	}
}
