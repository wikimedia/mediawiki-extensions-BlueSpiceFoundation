<?php

namespace BlueSpice\Data;

interface IReader {

	/**
	 *
	 * @param  ReaderParams $params
	 * @return ResultSet
	 */
	public function read( $params );

	/**
	 * @return Schema Column definition compatible to
	 * https://docs.sencha.com/extjs/4.2.1/#!/api/Ext.grid.Panel-cfg-columns
	 */
	public function getSchema();
}
