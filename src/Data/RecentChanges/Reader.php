<?php

namespace BlueSpice\Data\RecentChanges;

use BlueSpice\Data\ReaderParams;
use BlueSpice\Data\DatabaseReader;
use MWNamespace;
use Title;

class Reader extends DatabaseReader {

	/**
	 *
	 * @param ReaderParams $params
	 * @return PrimaryDataProvider
	 */
	protected function makePrimaryDataProvider( $params ) {
		$contentNamespaceIds = $this->getContentNamespaceIds();
		$namespaceWhitelist = $this->filterNamespacesByReadPermission( $contentNamespaceIds );
		return new PrimaryDataProvider(
			$this->db,
			$namespaceWhitelist
		);
	}

	/**
	 *
	 * @return SecondaryDataProvider
	 */
	protected function makeSecondaryDataProvider() {
		return new SecondaryDataProvider(
			\MediaWiki\MediaWikiServices::getInstance()->getLinkRenderer(),
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

	/**
	 *
	 * @param array $nsIds
	 * @return array
	 */
	private function filterNamespacesByReadPermission( $nsIds ) {
		$filteredNamespaceIds = [];
		$user = $this->context->getUser();
		foreach ( $nsIds  as $nsId ) {
			$dummyTitle = Title::makeTitle( $nsId, 'Dummy' );
			// In `REL1_35` replace this with `PermissionManager` call
			$userCanRead = $dummyTitle->userCan( 'read', $user );
			if ( $userCanRead ) {
				$filteredNamespaceIds[] = $nsId;
			}
		}
		return $filteredNamespaceIds;
	}

}
