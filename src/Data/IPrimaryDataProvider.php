<?php

namespace BlueSpice\Data;

interface IPrimaryDataProvider {

	/**
	 *
	 * @param ReaderParams $params Having it here allows us to prefilter and
	 * tweak performance
	 * @return Record[]
	 */
	public function makeData( $params );
}
