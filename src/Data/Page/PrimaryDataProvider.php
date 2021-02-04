<?php

namespace BlueSpice\Data\Page;

use BlueSpice\Data\PrimaryDatabaseDataProvider;
use BlueSpice\Data\ReaderParams;
use IContextSource;
use MediaWiki\MediaWikiServices;
use Title;
use User;
use Wikimedia\Rdbms\IDatabase;

class PrimaryDataProvider extends PrimaryDatabaseDataProvider {

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
		return new Record( (object)[
			Record::ID => $title->getArticleID(),
			Record::NS => $title->getNamespace(),
			Record::TITLE => $title->getDBkey(),
			Record::IS_REDIRECT => $title->isRedirect(),
			Record::IS_NEW => $title->isNewPage(),
			Record::TOUCHED => $title->getTouched(),
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
	 * @param ReaderParams $params
	 * @return array
	 */
	protected function makePreOptionConds( ReaderParams $params ) {
		return [];
	}

	/**
	 *
	 * @param Title $title
	 * @return bool
	 */
	protected function userCanRead( \Title $title ) {
		if ( $this->isSystemUser( $this->context->getUser() ) ) {
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
