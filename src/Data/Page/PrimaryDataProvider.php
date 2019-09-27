<?php

namespace BlueSpice\Data\Page;

use IContextSource;
use Title;
use User;
use Hooks;
use Wikimedia\Rdbms\IDatabase;
use BlueSpice\Services;
use BlueSpice\Data\ReaderParams;
use BlueSpice\Data\PrimaryDatabaseDataProvider;

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
	 * @return null
	 */
	protected function appendRowToData( $row ) {
		$title = Title::newFromRow( $row );
		if ( !$title || !$this->userCanRead( $title ) ) {
			return;
		}

		$record = $this->getRecordFromTitle( $title );
		Hooks::run( 'BSPageStoreDataProviderBeforeAppendRow', [
			$this,
			$record,
			$title,
		] );
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
		return $title->userCan( 'read', $this->context->getUser() );
	}

	/**
	 *
	 * @param User $user
	 * @return bool
	 */
	protected function isSystemUser( User $user ) {
		return Services::getInstance()->getBSUtilityFactory()
			->getMaintenanceUser()->isMaintenanceUser( $user );
	}
}
