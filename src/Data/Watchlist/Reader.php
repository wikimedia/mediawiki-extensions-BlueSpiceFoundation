<?php

namespace BlueSpice\Data\Watchlist;

use \BlueSpice\Data\DatabaseReader;

class Reader extends DatabaseReader {

	protected function makePrimaryDataProvider( $params ) {
		return new PrimaryDataProvider( $this->db );
	}

	protected function makeSecondaryDataProvider() {
		return new SecondaryDataProvider(
			\MediaWiki\MediaWikiServices::getInstance()->getLinkRenderer()
		);
	}

	public function getSchema() {
		return new Schema();
	}
}
