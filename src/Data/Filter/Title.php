<?php

namespace BlueSpice\Data\Filter;

class Title extends Range {

	/**
	 * Performs string filtering based on given filter of type Title on a
	 * dataset
	 *
	 * @param \BlueSpice\Data\Record $dataSet
	 * @return boolean
	 */
	protected function doesMatch( $dataSet ) {
		if( !is_string( $this->getValue() ) ) {
			return true; //TODO: Warning
		}
		$fieldValue = \Title::newFromText(
			$dataSet->get($this->getField() ),
			$this->getDefaultTitleNamespace()
		);
		$filterValue = \Title::newFromText(
			$this->getValue(),
			$this->getDefaultTitleNamespace()
		);

		switch( $this->getComparison() ) {
			case self::COMPARISON_GREATER_THAN:
				return \Title::compare( $fieldValue, $filterValue ) > 0;
			case self::COMPARISON_LOWER_THAN:
				return \Title::compare( $fieldValue, $filterValue ) < 0;
			case self::COMPARISON_EQUALS:
				return \Title::compare( $fieldValue, $filterValue ) == 0;
			case self::COMPARISON_NOT_EQUALS:
				return \Title::compare( $fieldValue, $filterValue ) != 0;
		}
		return true;
	}

	protected function getDefaultTitleNamespace() {
		return NS_MAIN;
	}

}
