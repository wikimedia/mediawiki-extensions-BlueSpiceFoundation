<?php

namespace BlueSpice\Data;

class NullTrimmer implements ITrimmer {

	/**
	 *
	 * @param \BlueSpice\Data\Record[] $dataSets
	 * @return \BlueSpice\Data\Record[]
	 */
	public function trim( $dataSets ) {
		return $dataSets;
	}
}
