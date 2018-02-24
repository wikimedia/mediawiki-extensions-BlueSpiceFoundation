<?php

namespace BlueSpice\Data;

class ResultSet extends RecordSet {

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
		parent::__construct( $records );
		$this->total = $total;
	}

	/**
	 *
	 * @return int
	 */
	public function getTotal() {
		return $this->total;
	}
}
