<?php

namespace BlueSpice\Data\Watchlist;

use \BlueSpice\Data\IPrimaryDataProvider;
use \BlueSpice\Data\Filter;
use \BlueSpice\Data\FilterFinder;

class PrimaryDataProvider implements IPrimaryDataProvider {

	/**
	 *
	 * @var \BlueSpice\Data\Record
	 */
	protected $data = [];

	/**
	 *
	 * @var int[]
	 */
	protected $userIds = [];

	/**
	 *
	 * @var int[]
	 */
	protected $namespaceIds = [];

	/**
	 *
	 * @var \Wikimedia\Rdbms\IDatabase
	 */
	protected $db = null;

	/**
	 *
	 * @param \Wikimedia\Rdbms\IDatabase $db
	 */
	public function __construct( $db ) {
		$this->db = $db;
	}

	/**
	 *
	 * @param \BlueSpice\Data\ReaderParams $params
	 */
	public function makeData( $params ) {
		$res = $this->db->select(
			'watchlist',
			'*',
			$this->makePreFilterConds( $params->getFilter() )
		);

		$distinctUserIds = [];
		$distinctNamespaceIds = [];
		foreach( $res as $row ) {
			$distinctUserIds[$row->wl_user] = true;
			$distinctNamespaceIds[$row->wl_namespace] = true;
			$this->appendRowToData( $row );
		}

		$this->userIds = array_keys( $distinctUserIds );
		$this->addUserFields();

		$this->namespaceIds = array_keys( $distinctNamespaceIds );
		$this->addTitleFields();

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
		$userIdFilter = $filterFinder->findByField( 'user_id' );

		if( $userIdFilter instanceof Filter ) {
			$conds['wl_user'] = $userIdFilter->getValue();
		}

		return $conds;
	}

	protected function appendRowToData( $row ) {
		$title = \Title::makeTitle( $row->wl_namespace, $row->wl_title );

		$this->data[] = new Record( (object) [
			Record::USER_ID => $row->wl_user,
			Record::USER_DISPLAY_NAME => '',
			Record::USER_LINK => '',
			Record::PAGE_ID => '',
			Record::PAGE_PREFIXED_TEXT => $title->getPrefixedText(), //Not expensive, as all required information available on instantiation
			Record::PAGE_LINK => '-',
			Record::NOTIFICATIONTIMESTAMP => $row->wl_notificationtimestamp,
			Record::HAS_UNREAD_CHANGES => $row->wl_notificationtimestamp !== null,
			Record::IS_TALK_PAGE => $title->isTalkPage()
		] );
	}

	protected function addUserFields() {
		$res = $this->db->select(
			'user',
			[ 'user_id', 'user_name', 'user_real_name' ],
			[ 'user_id' => $this->userIds ]
		);

		$userDisplayNames = [];
		foreach( $res as $row ) {
			$userDisplayNames[ $row->user_id ] = $row->user_real_name != null
				? $row->user_real_name
				: $row->user_name;
		}

		foreach( $this->data as &$dataSet ) {
			$userId = $dataSet->get( Record::USER_ID );
			//users may have been deleted from user table but still remain in
			//watchlist
			if( !isset( $userDisplayNames[$userId] ) ) {
				continue;
			}
			$dataSet->set( Record::USER_DISPLAY_NAME, $userDisplayNames[$userId] );
		}
	}

	protected function addTitleFields() {
		$res = $this->db->select(
			'page',
			[ 'page_id', 'page_title', 'page_namespace' ],
			[ 'page_namespace' => $this->namespaceIds ] //TODO maybe also add a collection of "page_title"s to narrow result
		);

		$pageIds = [];
		foreach( $res as $row ) {
			$title = \Title::makeTitle( $row->page_namespace, $row->page_title );
			$pageIds[$title->getPrefixedText()] = $row->page_id;
		}

		foreach( $this->data as &$dataSet ) {
			$pagePrefixedText = $dataSet->get( Record::PAGE_PREFIXED_TEXT );
			$pageId = 0;

			//It is possible to watch non existing pages
			if( isset( $pageIds[$pagePrefixedText] ) ) {
				$pageId = $pageIds[$pagePrefixedText];
			}
			$dataSet->set( Record::PAGE_ID, $pageId );
		}
	}
}
