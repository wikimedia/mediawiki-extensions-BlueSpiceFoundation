<?php

namespace BlueSpice\Data\Watchlist;

use \BlueSpice\Data\DatabaseReader;
use MWNamespace;

class Reader extends DatabaseReader {

	protected function makePrimaryDataProvider( $params ) {
		$contentNamespaces = MWNamespace::getContentNamespaces();
		return new PrimaryDataProvider( $this->db, $contentNamespaces );
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
