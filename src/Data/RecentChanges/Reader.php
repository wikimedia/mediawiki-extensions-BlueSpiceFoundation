<?php

namespace BlueSpice\Data\RecentChanges;

use \BlueSpice\Data\DatabaseReader;
use MWNamespace;

class Reader extends DatabaseReader {

	protected function makePrimaryDataProvider( $params ) {
		return new PrimaryDataProvider(
			$this->db,
			$this->getContentNamespaceIds()
		);
	}

	protected function makeSecondaryDataProvider() {
		return new SecondaryDataProvider(
			\MediaWiki\MediaWikiServices::getInstance()->getLinkRenderer(),
			$this->context
		);
	}

	public function getSchema() {
		return new Schema();
	}

	protected function getContentNamespaceIds() {
		$namespaceIds = $this->context->getLanguage()->getNamespaceIds();
		$contentNamespaceIds = [];

		foreach( $namespaceIds as $namespaceId ) {
			if( MWNamespace::isContent( $namespaceId ) ) {
				$contentNamespaceIds[] = $namespaceId;
			}
		}

		return $contentNamespaceIds;
	}

}
