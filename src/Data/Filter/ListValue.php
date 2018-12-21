<?php

namespace BlueSpice\Data\Filter;

use BlueSpice\Data\Filter;

class ListValue extends Filter {
	const COMPARISON_IN = 'in';
	const COMPARISON_CONTAINS = 'ct';
	const COMPARISON_NOT_CONTAINS = 'nct';

	/**
	 *
	 * @param array $params
	 */
	public function __construct( $params ) {
		if( !isset( $params[self::KEY_COMPARISON] ) ) {
			$params[self::KEY_COMPARISON] = static::COMPARISON_IN;
		}
		parent::__construct( $params );
	}

	/**
	 * Performs list filtering based on given filter of type array on a dataset
	 * @param \BlueSpice\Data\Record $dataSet
	 * @return boolean
	 */
	protected function doesMatch( $dataSet ) {
		if( !is_array( $this->getValue() ) ) {
			return true; //TODO: Warning
		}
		$fieldValues = $dataSet->get( $this->getField() );
		if( empty( $fieldValues ) ) {
			return false;
		}
		if( is_string( $fieldValues ) ) {
			$fieldValues = [ $fieldValues ];
		}

		$intersection = array_intersect( $fieldValues, $this->getValue() );

		if( $this->getComparison() === static::COMPARISON_CONTAINS || $this->getComparison() === static::COMPARISON_IN ) {
			if ( empty( $intersection ) ) {
				return false;
			}
		}
		if( $this->getComparison() === static::COMPARISON_NOT_CONTAINS && !empty( $intersection ) ) {
			return false;
		}
		return true;
	}
}
