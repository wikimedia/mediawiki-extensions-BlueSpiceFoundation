<?php

namespace BlueSpice\Data\Filter;

use BlueSpice\Data\Filter;
use BsStringHelper;

/**
 * Class name "String" is reserved
 */
class StringValue extends Filter {
	public const COMPARISON_STARTS_WITH = 'sw';
	public const COMPARISON_ENDS_WITH = 'ew';
	public const COMPARISON_CONTAINS = 'ct';
	public const COMPARISON_NOT_CONTAINS = 'nct';

	public const COMPARISON_LIKE = 'like';

	/**
	 * Performs string filtering based on given filter of type string on a
	 * dataset
	 * @param \BlueSpice\Data\Record $dataSet
	 * @return bool
	 */
	protected function doesMatch( $dataSet ) {
		$fieldValues = $dataSet->get( $this->getField() );
		if ( !is_array( $fieldValues ) ) {
			$fieldValues = [ $fieldValues ];
		}
		foreach ( $fieldValues as $fieldValue ) {
			if ( !is_scalar( $fieldValue ) ) {
				continue;
			}
			$res = BsStringHelper::filter(
				$this->getComparison(),
				(string)$fieldValue,
				$this->getValue()
			);
			if ( $res ) {
				return true;
			}
		}
		return false;
	}
}
