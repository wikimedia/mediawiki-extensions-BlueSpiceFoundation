<?php

namespace BlueSpice\Data\Categorylinks;

use IContextSource;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\DataStore\Filter;
use MWStake\MediaWiki\Component\DataStore\FilterFinder;
use MWStake\MediaWiki\Component\DataStore\IPrimaryDataProvider;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;
use Title;
use User;

class PrimaryDataProvider implements IPrimaryDataProvider {

	/**
	 *
	 * @var \BlueSpice\Data\Record
	 */
	protected $data = [];

	/**
	 *
	 * @var \Wikimedia\Rdbms\IDatabase
	 */
	protected $db = null;

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
			'categorylinks',
			'*',
			$filterConds
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
			$conds['cl_from'] = $pageId->getValue();
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
		$title = Title::makeTitle( NS_CATEGORY, $row->cl_to );
		if ( !$title->isValid() ) {
			return;
		}

		$this->data[] = new Record( (object)[
			Record::PAGE_ID => $row->cl_from,
			Record::CATEGORY_TITLE => $title->getText(),
			Record::CATEGORY_LINK => '-'
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
		return $title->userCan( 'read', $this->context->getUser() );
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
