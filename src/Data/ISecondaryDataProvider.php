<?php

namespace BlueSpice\Data;

interface ISecondaryDataProvider {

	/**
	 *
	 * @param \BlueSpice\Data\Record[] $dataSets
	 * @return \BlueSpice\Data\Record[]
	 */
	public function extend( $dataSets );
}
