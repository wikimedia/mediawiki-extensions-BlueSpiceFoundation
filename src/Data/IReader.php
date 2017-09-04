<?php

namespace BlueSpice\Data;

interface IReader {

	/**
	 *
	 * @param  ReaderParams $params
	 * @return ResultSet
	 */
	public function read( $params );
}