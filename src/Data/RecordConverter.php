<?php

namespace BlueSpice\Data;

class RecordConverter {

	/**
	 *
	 * @var Record[]
	 */
	protected $records = [];

	/**
	 *
	 * @param Record[] $records
	 */
	public function __construct( $records ) {
		$this->records = $records;
	}

	public function convertToRawData() {
		$rawData = [];
		foreach( $this->records as $record ) {
			$rawData[] = $record->getData();
		}
		return $rawData;
	}
}
