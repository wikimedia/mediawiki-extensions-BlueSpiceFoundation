<?php

namespace BlueSpice\Data\Page;

use IContextSource;
use Title;
use User;
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
		$fields = [ Record::ID, Record::NS, Record::TITLE, Record::IS_REDIRECT,
			Record::ID_NEW, Record::TOUCHED, Record::LATEST, Record::CONTENT_MODEL ];
		$data = [];
		foreach ( $fields as $key ) {
			$data[ $key ] = $row->{$key};
		}
		$this->data[] = new Record( (object)$data );
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
