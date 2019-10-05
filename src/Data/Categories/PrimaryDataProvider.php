<?php

namespace BlueSpice\Data\Categories;

use \BlueSpice\Data\IPrimaryDataProvider;
use BlueSpice\Data\ReaderParams;
use Title;
use stdClass;
use Wikimedia\Rdbms\IDatabase;
use Wikimedia\Rdbms\IResultWrapper;

class PrimaryDataProvider implements IPrimaryDataProvider {

	/**
	 *
	 * @var Record[]
	 */
	protected $data = [];

	/**
	 *
	 * @var IDatabase
	 */
	protected $db = null;

	/**
	 *
	 * @var int[]
	 */
	protected $namespaceWhitelist = [];

	/**
	 *
	 * @param IDatabase $db
	 * @param array $namespaceWhitelist
	 */
	public function __construct( $db, $namespaceWhitelist = [] ) {
		$this->db = $db;
		$this->namespaceWhitelist = $namespaceWhitelist;
	}

	/**
	 *
	 * @param ReaderParams $params
	 * @return Record[]
	 */
	public function makeData( $params ) {
		$res = $this->getCategoryRows();

		foreach ( $res as $row ) {
			$this->appendRowToData( $row );
		}

		return $this->data;
	}

	/**
	 *
	 * @param stdClass $row
	 */
	protected function appendRowToData( $row ) {
		$category = \Category::newFromRow( $row );
		if ( !$category->getTitle() instanceof Title ) {
			return;
		}

		$this->data[] = new Record( (object)[
			Record::CAT_ID => $category->getID(),
			Record::CAT_TITLE => $category->getName(),
			Record::CAT_PAGES => $category->getPageCount(),
			Record::CAT_SUBCATS => $category->getSubcatCount(),
			Record::CAT_FILES => $category->getFileCount()
		] );
	}

	/**
	 * @return IResultWrapper
	 */
	protected function getCategoryRows() {
		return $this->db->select(
			'category',
			[ 'cat_id', 'cat_title', 'cat_pages', 'cat_subcats', 'cat_files' ],
			[],
			__METHOD__
		);
	}
}
