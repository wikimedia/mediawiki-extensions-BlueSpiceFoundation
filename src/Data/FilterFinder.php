<?php

namespace BlueSpice\Data;

class FilterFinder {

	/**
	 *
	 * @var Filter[]
	 */
	protected $filters = [];

	/**
	 *
	 * @param Filter[] $filters
	 */
	public function __construct( $filters ) {
		$this->filters = $filters;
	}

	/**
	 *
	 * @param string $fieldName
	 * @return Filter|null
	 */
	public function findByField( $fieldName ) {
		foreach( $this->filters as $filter ) {
			if( $filter->getField() === $fieldName ) {
				return $filter;
			}
		}
		return null;
	}
}
