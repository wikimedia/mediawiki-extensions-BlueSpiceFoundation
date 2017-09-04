<?php

namespace BlueSpice\Data;

interface IWriter {

	/**
	 *
	 * @param array $dataSet
	 * @return \Status
	 */
	public function write( $dataSet );
}