<?php

namespace BlueSpice\Data\RecentChanges;

use MediaWiki\Title\Title;
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

	/**
	 *
	 * @var int[]
	 */
	protected $namespaceWhitelist = [];

	/**
	 *
	 * @param \Wikimedia\Rdbms\IDatabase $db
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
		$conds = [];

		if ( !empty( $this->namespaceWhitelist ) ) {
			$conds['rc_namespace'] = $this->namespaceWhitelist;
		}

		$info = \RecentChange::getQueryInfo();
		$res = $this->db->select(
			$info['tables'],
			$info['fields'],
			$conds,
			__METHOD__,
			[ 'ORDER BY' => 'rc_timestamp DESC' ],
			$info['joins']
		);

		foreach ( $res as $row ) {
			$this->appendRowToData( $row );
		}

		return $this->data;
	}

	/**
	 *
	 * @param \stdClass $row
	 */
	protected function appendRowToData( $row ) {
		$title = Title::makeTitle( $row->rc_namespace, $row->rc_title );

		$this->data[] = new Record( (object)[
			Record::USER_NAME => $row->rc_user_text,
			Record::USER_DISPLAY_NAME => '',
			Record::USER_LINK => '',
			// Not expensive, as all required information available on instantiation
			Record::PAGE_PREFIXED_TEXT => $title->getPrefixedText(),
			Record::PAGE_NAMESPACE => $row->rc_namespace,
			Record::PAGE_LINK => '',
			Record::TIMESTAMP => '',
			Record::RAW_TIMESTAMP => $row->rc_timestamp,
			Record::COMMENT_TEXT => htmlspecialchars( $row->rc_comment_text ),
			Record::SOURCE => $row->rc_source,
			Record::DIFF_URL => '',
			Record::DIFF_LINK => '',
			Record::HIST_URL => '',
			Record::HIST_LINK => '',
			Record::OLDID_URL => '',
			Record::OLDID_LINK => '',
			Record::CUR_ID => $row->rc_cur_id,
			Record::LAST_OLDID => $row->rc_last_oldid,
			Record::THIS_OLDID => $row->rc_this_oldid,
			Record::EXISTS => $title->exists(),
			'tmp_user' => $row->rc_user
		] );
	}
}
