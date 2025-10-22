<?php

use MediaWiki\Api\ApiBase;
use MediaWiki\Title\Title;
use Wikimedia\ParamValidator\ParamValidator;

class BSApiWikiSubPageTreeStore extends BSApiExtJSStoreBase {
	protected $root = 'children';

	/**
	 *
	 * @param string $sQuery
	 * @return stdClass[]
	 */
	public function makeData( $sQuery = '' ) {
		$sNode = $this->getParameter( 'node' );
		$aOptions = $this->getParameter( 'options' );

		if ( empty( $sNode ) ) {
			return $this->makeNamespaceNodes( $sQuery, $aOptions );
		}

		$aNodeTextParts = explode( ':', $sNode, 2 );
		if ( empty( $aNodeTextParts[1] ) ) {
			return $this->makeRootPageNodes( $aNodeTextParts[0], $sQuery, $aOptions );
		}

		$oParent = Title::newFromText( $sNode );
		return $this->makePageNodes( $oParent, $sQuery, $aOptions );
	}

	/**
	 *
	 * @return array
	 */
	public function getAllowedParams() {
		return parent::getAllowedParams() + [
			'node' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_DEFAULT => '',
				ApiBase::PARAM_HELP_MSG => 'apihelp-bs-wikisubpage-treestore-param-node',
			],
			'options' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_DEFAULT => '{}',
				ParamValidator::PARAM_REQUIRED => false,
				ApiBase::PARAM_HELP_MSG => 'apihelp-bs-wikisubpage-treestore-param-options',
			]
		];
	}

	/**
	 *
	 * @param string $sQuery
	 * @param array $aOptions
	 * @return stdClass[]
	 */
	protected function makeNamespaceNodes( $sQuery, $aOptions = [] ) {
		$aNamespaceIds = $this->getLanguage()->getNamespaceIds();
		$aDataSets = [];
		foreach ( $aNamespaceIds as $iNamespaceId ) {
			if ( $iNamespaceId < 0 ) {
				continue;
			}

			$oDummyTitle = Title::makeTitle( $iNamespaceId, 'X' );
			if ( !$this->services->getPermissionManager()
				->userCan( 'read', $this->getUser(), $oDummyTitle )
			) {
				continue;
			}

			$sNodeText = $oDummyTitle->getNsText();
			if ( $iNamespaceId === NS_MAIN ) {
				$sNodeText = wfMessage( 'bs-ns_main' )->text();
			}

			$oDataSet = new stdClass();
			$oDataSet->text = $sNodeText;
			// != $sNodeText
			$oDataSet->id = $oDummyTitle->getNsText() . ':';
			$oDataSet->isNamespaceNode = true;
			$oDataSet->leaf = false;
			$oDataSet->expanded = false;
			$oDataSet->loaded = false;

			$aDataSets[] = $oDataSet;
		}

		return $aDataSets;
	}

	/**
	 *
	 * @param string $sNamespacePrefix
	 * @param string $sQuery
	 * @param array $aOptions
	 * @return stdClass[]
	 */
	protected function makeRootPageNodes( $sNamespacePrefix, $sQuery, $aOptions = [] ) {
		$aDataSets = [];

		$oDummyTitle = Title::newFromText( $sNamespacePrefix . ':X' );
		$iNamespaceId = $oDummyTitle->getNamespace();
		$res = $this->getDB()->select(
			'page',
			'*',
			[
				'page_namespace' => $iNamespaceId
			],
			__METHOD__
		);

		foreach ( $res as $row ) {
			// Unfortunately there is not "NOT LIKE" in MW DBAL, therefore we
			// filter out subpages manually
			if ( strpos( $row->page_title, '/' ) !== false ) {
				continue;
			}

			$this->addDataSet( $aDataSets, $row );
		}

		return $aDataSets;
	}

	/**
	 *
	 * @param Title $oParent
	 * @param string $sQuery
	 * @param array $aOptions
	 * @return array of objects
	 */
	protected function makePageNodes( $oParent, $sQuery, $aOptions = [] ) {
		$aDataSets = [];

		$res = $this->getDB()->select(
			'page',
			'*',
			[
				'page_title ' . $this->getDB()->buildLike(
					$oParent->getDBkey() . '/',
					$this->getDB()->anyString()
				),
				'page_namespace' => $oParent->getNamespace()
			],
			__METHOD__
		);

		$sortedRes = [];
		foreach ( $res as $row ) {
			$sortedRes[$row->page_title] = $row;
		}
		ksort( $sortedRes, SORT_NATURAL );

		foreach ( $sortedRes as $row ) {
			$this->addDataSet( $aDataSets, $row, $oParent );
		}

		return $aDataSets;
	}

	/**
	 *
	 * @var array
	 */
	private $currentLevelIds = [];

	/**
	 *
	 * @param stdClass[] &$aDataSets
	 * @param stdClass $row
	 * @param Title|null $oParent
	 * @return void
	 */
	protected function addDataSet( &$aDataSets, $row, $oParent = null ) {
		$oTitle = Title::newFromRow( $row );
		if ( $oParent instanceof Title ) {
			$oBaseTitle = $oTitle->getBaseTitle();

			/*
			 * Handle gaps
			 * There could be the case that only the following pages are in DB
			 *  - A
			 *  - A/B/C
			 *  - A/B/D
			 */
			while ( !$oBaseTitle->exists() && !$oTitle->getBaseTitle()->equals( $oParent ) ) {
				$oTitle = $oBaseTitle;
				$oBaseTitle = $oTitle->getBaseTitle();
			}
			if ( !$oBaseTitle->equals( $oParent ) ) {
				// We want only direct children
				return;
			}
		}

		if ( !( $this->services->getPermissionManager()
			->userCan( 'read', $this->getUser(), $oTitle )
		) ) {
			return;
		}

		$id = $oTitle->getPrefixedDBkey();
		if ( $oTitle->getNamespace() === NS_MAIN ) {
			// Rebuild full qualified path
			$id = ':' . $id;
		}
		if ( in_array( $id, $this->currentLevelIds ) ) {
			// Prevent duplicate nodes
			return;
		}

		$this->currentLevelIds[] = $id;

		$oDataSet = new stdClass();
		$oDataSet->text = $oTitle->getSubpageText();
		$oDataSet->id = $id;
		$oDataSet->page_link = $this->oLinkRenderer->makeLink(
			$oTitle,
			$oTitle->getSubpageText()
		);
		$oDataSet->leaf = true;
		$oDataSet->expanded = true;
		$oDataSet->loaded = true;

		if ( $oTitle->hasSubpages() ) {
			$oDataSet->leaf = false;
			$oDataSet->expanded = false;
			$oDataSet->loaded = false;
		}

		$aDataSets[] = $oDataSet;
	}

	/**
	 * Pagination in a tree store is not reasonable
	 * @param stdClass[] $aProcessedData
	 * @return stdClass[]
	 */
	public function trimData( $aProcessedData ) {
		return $aProcessedData;
	}
}
