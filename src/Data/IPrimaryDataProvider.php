<?php

namespace BlueSpice\Data;

interface IPrimaryDataProvider {

	/**
	 *
	 * @param string $query Special simple filter that aims at one specific
	 * field that the DataProvider needs to define.
	 * @param Filter[] $preFilters Complete set of filters that will also be
	 * applied later during the process by the "Filterer" step. Having it here
	 * allows us to prefilter and tweak performance
	 * @return \BlueSpice\Data\Record[]
	 */
	public function makeData( $query = '', $preFilters = [] );
}