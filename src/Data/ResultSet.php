<?php

namespace BlueSpice\Data;

class ResultSet {

	/**
	 *
	 * @var \stdClass[]
	 */
	protected $records = [];

	/**
	 *
	 * @var int
	 */
	protected $total = 0;

	/**
	 *
	 * @param \stdClass[] $records
	 * @param int $total
	 */
	public function __construct( $records, $total ) {
		$this->records = $records;
		$this->total = $total;
	}

	public function getRecords() {
		return $this->records;
	}

	public function getTotal() {
		return $this->total;
	}
}