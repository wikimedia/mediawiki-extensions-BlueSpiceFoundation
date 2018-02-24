<?php

namespace BlueSpice\Data;

class RecordSet {

	/**
	 *
	 * @var \BlueSpice\Data\Record[]
	 */
	protected $records = [];

	/**
	 *
	 * @param \BlueSpice\Data\Record[] $records
	 */
	public function __construct( $records ) {
		$this->records = $records;
	}

	/**
	 *
	 * @return \BlueSpice\Data\Record[]
	 */
	public function getRecords() {
		return $this->records;
	}
}
