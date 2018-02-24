<?php

namespace BlueSpice\Data\Filter;

class Numeric extends Range {
	/**
	 * Performs numeric filtering based on given filter of type integer on a
	 * dataset
	 *
	 * @param \BlueSpice\Data\Record $dataSet
	 * @return boolean
	 */
	protected function doesMatch( $dataSet ) {
		if( !is_numeric( $this->getValue() ) ) {
			return true; //TODO: Warning
		}
		$fieldValue = (int) $dataSet->get( $this->getField() );
		$filterValue = (int) $this->getValue();

		switch( $this->getComparison() ) {
			case self::COMPARISON_GREATER_THAN:
				return $fieldValue > $filterValue;
			case self::COMPARISON_LOWER_THAN:
				return $fieldValue < $filterValue;
			case self::COMPARISON_EQUALS:
				return $fieldValue === $filterValue;
			case self::COMPARISON_NOT_EQUALS:
				return $fieldValue !== $filterValue;
		}
		return true;
	}
}
