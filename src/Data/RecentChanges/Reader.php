<?php

namespace BlueSpice\Data\RecentChanges;

use BlueSpice\Data\DatabaseReader;
use BlueSpice\Data\ReaderParams;
use BlueSpice\Services;
use MWNamespace;

class Reader extends DatabaseReader {

	/**
	 *
	 * @param ReaderParams $params
	 * @return PrimaryDataProvider
	 */
	protected function makePrimaryDataProvider( $params ) {
		return new PrimaryDataProvider(
			$this->db,
			$this->getContentNamespaceIds()
		);
	}

	/**
	 *
	 * @return SecondaryDataProvider
	 */
	protected function makeSecondaryDataProvider() {
		return new SecondaryDataProvider(
			Services::getInstance()->getLinkRenderer(),
			$this->context
		);
	}

	/**
	 *
	 * @return Schema
	 */
	public function getSchema() {
		return new Schema();
	}

	/**
	 *
	 * @return array
	 */
	protected function getContentNamespaceIds() {
		$namespaceIds = $this->context->getLanguage()->getNamespaceIds();
		$contentNamespaceIds = [];

		foreach ( $namespaceIds as $namespaceId ) {
			if ( MWNamespace::isContent( $namespaceId ) ) {
				$contentNamespaceIds[] = $namespaceId;
			}
		}

		return $contentNamespaceIds;
	}

}
