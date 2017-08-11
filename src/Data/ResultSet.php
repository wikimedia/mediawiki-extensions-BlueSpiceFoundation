<?php

namespace BlueSpice\Data;

class ResultSet {

	/**
	 *
	 * @var \BlueSpice\Data\Record[]
	 */
	protected $records = [];

	/**
	 *
	 * @var int
	 */
	protected $total = 0;

	/**
	 *
	 * @param \BlueSpice\Data\Record[] $records
	 * @param int $total
	 */
	public function __construct( $records, $total ) {
		$this->records = $records;
		$this->total = $total;
	}

	/**
	 *
	 * @return \BlueSpice\Data\Record[]
	 */
	public function getRecords() {
		return $this->records;
	}

	/**
	 *
	 * @return int
	 */
	public function getTotal() {
		return $this->total;
	}
}