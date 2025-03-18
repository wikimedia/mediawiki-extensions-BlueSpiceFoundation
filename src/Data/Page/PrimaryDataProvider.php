<?php

namespace BlueSpice\Data\Page;

use MediaWiki\Context\IContextSource;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use MediaWiki\User\User;
use MWStake\MediaWiki\Component\DataStore\PrimaryDatabaseDataProvider;
use MWStake\MediaWiki\Component\DataStore\ReaderParams as DataStoreReaderParams;
use Wikimedia\Rdbms\IDatabase;

class PrimaryDataProvider extends PrimaryDatabaseDataProvider {

	/**
	 * Contains some title information, which should be pre-loaded for all titles with separate query.
	 * For now has such structure:
	 * [
	 *   <page_id1> => [
	 *     'page_is_new' => <page_is_new1>,
	 *     'page_touched' => <page_touched1>
	 *   ],
	 * 	 <page_id2> => [
	 *     'page_is_new' => <page_is_new2>,
	 *     'page_touched' => <page_touched2>
	 *   ],
	 *   ...
	 * ]
	 *
	 * @var array
	 */
	private $titlePreloadData;

	/**
	 * <tt>true</tt> if current context user is system user, <tt>false</tt> otherwise.
	 *
	 * @var bool
	 */
	protected $isSystemUser = false;

	/**
	 *
	 * @var IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @param IDatabase $db
	 * @param Schema $schema
	 * @param IContextSource $context
	 */
	public function __construct( IDatabase $db, $schema, $context ) {
		parent::__construct( $db, $schema );
		$this->context = $context;
	}

	/**
	 * Inits {@link PrimaryDataProvider::$titlePreloadData}
	 */
	protected function loadTitlesData() {
		$res = $this->db->select(
			'page',
			[
				'page_id',
				'page_is_new',
				'page_touched'
			],
			'',
			__METHOD__
		);

		foreach ( $res as $row ) {
			$this->titlePreloadData[$row->page_id]['page_is_new'] = (bool)$row->page_is_new;
			$this->titlePreloadData[$row->page_id]['page_touched'] = (string)$row->page_touched;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function makeData( $params ) {
		$this->loadTitlesData();

		$this->isSystemUser = $this->isSystemUser( $this->context->getUser() );

		return parent::makeData( $params );
	}

	/**
	 *
	 * @param \stdClass $row
	 */
	protected function appendRowToData( \stdClass $row ) {
		$title = Title::newFromRow( $row );
		if ( !$title || !$this->userCanRead( $title ) ) {
			return;
		}

		$record = $this->getRecordFromTitle( $title );
		MediaWikiServices::getInstance()->getHookContainer()->run(
			'BSPageStoreDataProviderBeforeAppendRow',
			[
				$this,
				$record,
				$title,
			]
		);
		if ( $record ) {
			$this->data[] = $record;
		}
	}

	/**
	 * @param Title $title
	 * @return Record|false to skip given title
	 */
	protected function getRecordFromTitle( Title $title ) {
		$titleId = $title->getArticleID();

		return new Record( (object)[
			Record::ID => $titleId,
			Record::NS => $title->getNamespace(),
			Record::TITLE => $title->getDBkey(),
			Record::IS_REDIRECT => $title->isRedirect(),
			Record::IS_NEW => $this->titlePreloadData[$titleId]['page_is_new'],
			Record::TOUCHED => $this->titlePreloadData[$titleId]['page_touched'],
			Record::LATEST => $title->getLatestRevID(),
			Record::CONTENT_MODEL => $title->getContentModel()
		] );
	}

	/**
	 *
	 * @return array
	 */
	protected function getTableNames() {
		return [ Schema::TABLE_NAME ];
	}

	/**
	 *
	 * @param DataStoreReaderParams $params
	 * @return array
	 */
	protected function makePreOptionConds( DataStoreReaderParams $params ) {
		return [];
	}

	/**
	 *
	 * @param Title $title
	 * @return bool
	 */
	protected function userCanRead( Title $title ) {
		if ( $this->isSystemUser ) {
			return true;
		}
		return MediaWikiServices::getInstance()
			->getPermissionManager()
			->userCan( 'read', $this->context->getUser(), $title );
	}

	/**
	 *
	 * @param User $user
	 * @return bool
	 */
	protected function isSystemUser( User $user ) {
		return MediaWikiServices::getInstance()->getService( 'BSUtilityFactory' )
			->getMaintenanceUser()->isMaintenanceUser( $user );
	}
}
