<?php

namespace BlueSpice\Data;

class NullSorter extends Sorter {

	/**
	 *
	 * @param Record[] $dataSets
	 * @param array $unsortableProps
	 * @return Record[]
	 */
	public function sort( $dataSets, $unsortableProps = [] ) {
		return $dataSets;
	}
}
