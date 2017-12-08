<?php

namespace BlueSpice\Data\Filter;

use BlueSpice\Data\Filter;

class ListValue extends Filter {
	const COMPARISON_CONTAINS = 'ct';

	/**
	 *
	 * @param array $params
	 */
	public function __construct( $params ) {
		if( !isset( $params[self::KEY_COMPARISON] ) ) {
			$params[self::KEY_COMPARISON] = static::COMPARISON_CONTAINS;
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

		$intersection = array_intersect( $fieldValues, $this->getValue() );
		if( empty( $intersection ) ) {
			return false;
		}
		return true;
	}
}

