<?php

namespace BlueSpice\Data\Filter;

use BlueSpice\Data\Filter;

class Boolean extends Filter {

	/**
	 * Performs filtering based on given filter of type bool on a dataset
	 *
	 * @param \BlueSpice\Data\Record $dataSet
	 * @return boolean
	 */
	protected function doesMatch( $dataSet ) {
		$fieldValue = $dataSet->get( $this->getField() );
		$filterValue = $this->getValue();

		switch( $this->getComparison() ) {
			case self::COMPARISON_EQUALS:
				return $fieldValue == $filterValue;
			case self::COMPARISON_NOT_EQUALS:
				return $fieldValue != $filterValue;
		}
		return  false;
	}
}
