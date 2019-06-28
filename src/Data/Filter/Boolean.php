<?php

namespace BlueSpice\Data\Filter;

use BlueSpice\Data\Filter;

class Boolean extends Filter {
const COMPARISON_EQUALS_BOOL = '==';

	/**
	 * Performs filtering based on given filter of type bool on a dataset
	 *
	 * @param \BlueSpice\Data\Record $dataSet
	 * @return bool
	 */
	protected function doesMatch( $dataSet ) {
		$fieldValue = $dataSet->get( $this->getField() );
		$filterValue = $this->getValue();

		if ( $this->getComparison() === static::COMPARISON_EQUALS_BOOL ) {
			return $filterValue === $fieldValue;
		}

		// backwards compatibility
		switch ( $this->getComparison() ) {
			case self::COMPARISON_EQUALS:
				return $fieldValue == $filterValue;
			case self::COMPARISON_NOT_EQUALS:
				return $fieldValue != $filterValue;
		}
		return false;
	}
}
