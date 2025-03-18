<?php

namespace BlueSpice\Data\Watchlist;

use MediaWiki\MediaWikiServices;
use MediaWiki\Message\Message;
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
	 * @var array
	 */
	protected $namespaceWhitelist = [];

	/**
	 *
	 * @var User
	 */
	protected $user = null;

	/**
	 *
	 * @param \Wikimedia\Rdbms\IDatabase $db
	 * @param int[] $namespaceWhitelist
	 * @param User|null $user
	 */
	public function __construct( $db, $namespaceWhitelist, $user = null ) {
		$this->db = $db;
		$this->namespaceWhitelist = $namespaceWhitelist;
		$this->user = $user;
	}

	/**
	 *
	 * @param ReaderParams $params
	 * @return Record[]
	 */
	public function makeData( $params ) {
		$res = $this->db->select(
			'watchlist',
			'*',
			$this->makePreFilterConds( $params->getFilter() ),
			__METHOD__
		);

		$distinctUserIds = [];
		$distinctNamespaceIds = [];
		$userFactory = MediaWikiServices::getInstance()->getUserFactory();
		foreach ( $res as $row ) {
			$user = $userFactory->newFromId( (int)$row->wl_user );
			// leftover data from deleted users
			if ( !$user || !$user->isRegistered() ) {
				continue;
			}
			$distinctUserIds[(int)$row->wl_user] = true;
			$distinctNamespaceIds[(int)$row->wl_namespace] = true;
			$this->appendRowToData( $row );
		}

		if ( empty( $distinctUserIds ) || empty( $distinctNamespaceIds ) ) {
			return $this->data;
		}
		$this->userIds = array_keys( $distinctUserIds );
		$this->namespaceIds = array_keys( $distinctNamespaceIds );
		$this->addUserFields();
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

		if ( $userIdFilter instanceof Filter ) {
			$conds['wl_user'] = $userIdFilter->getValue();
		}

		if ( $this->user instanceof User ) {
			$conds['wl_user'] = $this->user->getId();
		}

		$conds['wl_namespace'] = array_values( $this->namespaceWhitelist );

		return $conds;
	}

	/**
	 *
	 * @param \stdClass $row
	 */
	protected function appendRowToData( $row ) {
		$title = Title::makeTitle( $row->wl_namespace, $row->wl_title );
		if ( !$title->isValid() ) {
			return;
		}

		$this->data[] = new Record( (object)[
			Record::USER_ID => $row->wl_user,
			Record::USER_DISPLAY_NAME => '',
			Record::USER_LINK => '',
			Record::PAGE_ID => '',
			// Not expensive, as all required information available on instantiation
			Record::PAGE_PREFIXED_TEXT => $title->getPrefixedText(),
			Record::PAGE_LINK => '-',
			Record::NOTIFICATIONTIMESTAMP => $row->wl_notificationtimestamp,
			Record::HAS_UNREAD_CHANGES => $row->wl_notificationtimestamp !== null,
			Record::IS_TALK_PAGE => $title->isTalkPage(),
			Record::UNREAD_CHANGES_DIFF_REVID => -1,
			Record::PAGE_NAMESPACE => $title->getNamespace(),
			Record::PAGE_NAMESPACE_TEXT => $title->getNamespace() === NS_MAIN ?
				Message::newFromKey( 'bs-ns_main' )->text() : $title->getNsText(),
		] );
	}

	protected function addUserFields() {
		$res = $this->db->select(
			'user',
			[ 'user_id', 'user_name', 'user_real_name' ],
			[ 'user_id' => $this->userIds ],
			__METHOD__
		);

		$userDisplayNames = [];
		foreach ( $res as $row ) {
			$userDisplayNames[ $row->user_id ] = $row->user_real_name != null
				? $row->user_real_name
				: $row->user_name;
		}

		foreach ( $this->data as &$dataSet ) {
			$userId = $dataSet->get( Record::USER_ID );
			// users may have been deleted from user table but still remain in
			// watchlist
			if ( !isset( $userDisplayNames[$userId] ) ) {
				continue;
			}
			$dataSet->set( Record::USER_DISPLAY_NAME, $userDisplayNames[$userId] );
		}
	}

	protected function addTitleFields() {
		$res = $this->db->select(
			'page',
			[ 'page_id', 'page_title', 'page_namespace' ],
			// TODO maybe also add a collection of "page_title"s to narrow result
			[ 'page_namespace' => $this->namespaceIds ],
			__METHOD__
		);

		$pageIds = [];
		foreach ( $res as $row ) {
			$title = Title::makeTitle( $row->page_namespace, $row->page_title );
			$pageIds[$title->getPrefixedText()] = $row->page_id;
		}

		foreach ( $this->data as &$dataSet ) {
			$pagePrefixedText = $dataSet->get( Record::PAGE_PREFIXED_TEXT );
			$pageId = 0;

			// It is possible to watch non existing pages
			if ( isset( $pageIds[$pagePrefixedText] ) ) {
				$pageId = $pageIds[$pagePrefixedText];
			}
			$dataSet->set( Record::PAGE_ID, $pageId );
		}
	}
}
