<?php

namespace BlueSpice\Data\RecentChanges;

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
			'recentchanges',
			'*',
			[]
		);

		foreach( $res as $row ) {
			$this->appendRowToData( $row );
		}

		return $this->data;
	}

	protected function appendRowToData( $row ) {
		$title = \Title::makeTitle( $row->rc_namespace, $row->rc_title );

		$this->data[] = new Record( (object) [
			Record::USER_NAME => $row->rc_user_text,
			Record::USER_DISPLAY_NAME => '',
			Record::USER_LINK => '',
			Record::PAGE_PREFIXED_TEXT => $title->getPrefixedText(), //Not expensive, as all required information available on instantiation
			Record::PAGE_NAMESPACE => $row->rc_namespace,
			Record::PAGE_LINK => '',
			Record::TIMESTAMP => '',
			Record::RAW_TIMESTAMP =>$row->rc_timestamp,
			Record::COMMENT_TEXT => $row->rc_comment,
			Record::SOURCE => $row->rc_source,
			Record::DIFF_URL => '',
			Record::DIFF_LINK => '',
			Record::HIST_URL => '',
			Record::HIST_LINK => '',
			'tmp_curid' => $row->rc_cur_id,
			'tmp_diff' => $row->rc_last_oldid,
			'tmp_oldid' => $row->rc_this_oldid,
			'tmp_user' => $row->rc_user
		] );
	}
}
