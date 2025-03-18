<?php

namespace BlueSpice\Data\Templatelinks;

use MediaWiki\Context\IContextSource;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use MediaWiki\User\User;
use MWStake\MediaWiki\Component\DataStore\Filter;
use MWStake\MediaWiki\Component\DataStore\FilterFinder;
use MWStake\MediaWiki\Component\DataStore\IPrimaryDataProvider;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;
use MWStake\MediaWiki\Component\DataStore\Record as DataStoreRecord;

class PrimaryDataProvider implements IPrimaryDataProvider {

	/**
	 *
	 * @var DataStoreRecord
	 */
	protected $data = [];

	/**
	 *
	 * @var \Wikimedia\Rdbms\IDatabase
	 */
	protected $db = null;

	/** @var IContextSource */
	protected $context = null;

	/**
	 *
	 * @var int
	 */
	protected $pageId = -1;

	/**
	 *
	 * @param \Wikimedia\Rdbms\IDatabase $db
	 * @param IContextSource|null $context
	 */
	public function __construct( $db, $context ) {
		$this->db = $db;
		$this->context = $context;
	}

	/**
	 *
	 * @param ReaderParams $params
	 * @return Record[]
	 */
	public function makeData( $params ) {
		$filterConds = $this->makePreFilterConds( $params->getFilter() );

		$title = Title::newFromID( $this->pageId );
		if ( !$this->userCanRead( $title ) ) {
			return [];
		}

		$res = $this->db->select(
			'templatelinks',
			'*',
			$filterConds,
			__METHOD__
		);

		foreach ( $res as $row ) {
			$this->appendRowToData( $row );
		}

		return $this->data;
	}

	/**
	 *
	 * @param Filter[] $preFilters
	 * @return array
	 */
	protected function makePreFilterConds( $preFilters ) {
		$conds = [];
		$filterFinder = new FilterFinder( $preFilters );
		$pageId = $filterFinder->findByField( 'page_id' );

		if ( $pageId instanceof Filter ) {
			$conds['tl_from'] = $pageId->getValue();
		}

		// Save page id for permission check
		$this->pageId = (int)$pageId->getValue();

		return $conds;
	}

	/**
	 *
	 * @param \stdClass $row
	 */
	protected function appendRowToData( $row ) {
		$title = Title::makeTitle( $row->tl_namespace, $row->tl_title );
		if ( !$title->isValid() ) {
			return;
		}

		$this->data[] = new Record( (object)[
			Record::PAGE_ID => $row->tl_from,
			Record::TEMPLATE_TITLE => $row->tl_title,
			Record::TEMPLATE_NS_ID => $row->tl_namespace,
			Record::TEMPLATE_LINK => '-'
		] );
	}

	/**
	 *
	 * @param Title $title
	 * @return bool
	 */
	protected function userCanRead( $title ) {
		if ( $this->isSystemUser( $this->context->getUser() ) ) {
			return true;
		}
		return MediaWikiServices::getInstance()->getPermissionManager()
			->userCan( 'read', $this->context->getUser(), $title );
	}

	/**
	 *
	 * @param User $user
	 * @return bool
	 */
	protected function isSystemUser( $user ) {
		return MediaWikiServices::getInstance()->getService( 'BSUtilityFactory' )
			->getMaintenanceUser()->isMaintenanceUser( $user );
	}

}
