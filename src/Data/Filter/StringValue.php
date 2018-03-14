<?php

namespace BlueSpice\Data\Filter;

use BlueSpice\Data\Filter;

/**
 * Class name "String" is reserved
 */
class StringValue extends Filter {
	const COMPARISON_STARTS_WITH = 'sw';
	const COMPARISON_ENDS_WITH = 'ew';
	const COMPARISON_CONTAINS = 'ct';
	const COMPARISON_NOT_CONTAINS = 'nct';

	const COMPARISON_LIKE = 'like';

	/**
	 * Performs string filtering based on given filter of type string on a
	 * dataset
	 * @param \BlueSpice\Data\Record $dataSet
	 */
	protected function doesMatch( $dataSet ) {
		$fieldValue = $dataSet->get( $this->getField() );

		return \BsStringHelper::filter( $this->getComparison(), $fieldValue, $this->getValue() );
	}
}
