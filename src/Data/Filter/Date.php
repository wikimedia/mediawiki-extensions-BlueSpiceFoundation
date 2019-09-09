<?php

namespace BlueSpice\Data\Filter;

class Date extends Range {

	/**
	 * Performs filtering based on given filter of type date on a dataset
	 * "Ext.ux.grid.filter.DateFilter" by default sends filter value in format
	 * of m/d/Y
	 * @param \BlueSpice\Data\Record $dataSet
	 * @return bool
	 */
	protected function doesMatch( $dataSet ) {
		// Format: "m/d/Y"
		$filterValue = strtotime( $this->getValue() );
		// Format "YmdHis", or something else...
		$fieldValue = strtotime( $dataSet->get( $this->getField() ) );

		switch ( $this->getComparison() ) {
			case self::COMPARISON_GREATER_THAN:
				return $fieldValue > $filterValue;
			case self::COMPARISON_LOWER_THAN:
				return $fieldValue < $filterValue;
			case self::COMPARISON_EQUALS:
				// We need to normalise the date on day-level
				$fieldValue = strtotime(
					date( 'm/d/Y', $fieldValue )
				);
				return $fieldValue === $filterValue;
		}
		return true;
	}
}
