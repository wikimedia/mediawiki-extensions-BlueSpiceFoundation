<?php

namespace BlueSpice\Data;

class Sorter {

	/**
	 *
	 * @var Sort[];
	 */
	protected $sorts = null;

	/**
	 *
	 * @param Sort[] $sorts
	 */
	public function __construct( $sorts ) {
		$this->sorts = $sorts;
	}

	/**
	 *
	 * @param \BlueSpice\Data\Record[] $dataSets
	 * @param array $unsortableProps
	 * @return \BlueSpice\Data\Record[]
	 */
	public function sort( $dataSets, $unsortableProps = [] ) {
		$numberOfSorts = count( $this->sorts );
		$sortParams = [];
		foreach ( $this->sorts as $sort ) {
			$property = $sort->getProperty();
			if( in_array( $property, $unsortableProps ) ) {
				continue;
			}

			$valuesOf{$property} = array();
			foreach( $dataSets as $idx => $dataSet ) {
				$valuesOf{$property}[$idx] =
					$this->getSortValue( $dataSet, $property );
			}

			$sortParams[] = $valuesOf{$property};
			$sortParams[] = $this->getSortDirection( $sort );
			$sortParams[] = $this->getSortFlags( $property );
		}

		if( !empty( $sortParams ) ) {
			$sortParams[] = &$dataSets;
			call_user_func_array( 'array_multisort', $sortParams );
		}

		$dataSets = array_values( $dataSets );
		return $dataSets;
	}

	/**
	 * Returns the flags for PHP 'array_multisort' function
	 * May be overridden by subclasses to provide different sort flags
	 * depending on the property
	 * @param string $property
	 * @return int see http://php.net/manual/en/array.constants.php for details
	 */
	protected function getSortFlags( $property ) {
		return SORT_NATURAL;
	}

	/**
	 * Returns the value a for a field a dataset is being sorted by.
	 * May be overridden by subclass to allow custom sorting
	 * @param \BlueSpice\Data\Record $dataSet
	 * @param string $property
	 * @return string
	 */
	protected function getSortValue( $dataSet, $property ) {
		$value = $dataSet->get( $property );
		if( is_array( $value ) ) {
			return $this->getSortValueFromList( $value, $dataSet, $property );
		}

		return $value;
	}

	/**
	 * Normalizes an array to a string value that can be used in sort logic.
	 * May be overridden by subclass to customize sorting.
	 * Assumes that array entries can be casted to string.
	 * @param array $values
	 * @param \BlueSpice\Data\Record $dataSet
	 * @param string $property
	 * @return string
	 */
	protected function getSortValueFromList( $values, $dataSet, $property ) {
		$combinedValue = '';
		foreach( $values as $value ) {
			// PHP 7 workaround. In PHP 7 cast throws no exception. It's a fatal error so no way to catch
			if( $this->canBeCastedToString( $value ) )
			{
				$combinedValue .= (string)$value;
			} else {
				$combinedValue .= FormatJson::encode( $value );
			}
		}
		return $combinedValue;
	}


	/**
	 * Checks if a array or object ist castable to string.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	protected function canBeCastedToString( $value ) {
		if ( !is_array( $value ) &&
			( !is_object( $value ) && settype( $value, 'string' ) !== false ) ||
			( is_object( $value ) && method_exists( $value, '__toString' ) ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 *
	 * @param Sort $sort
	 * @return int Constant value of SORT_ASC or SORT_DESC
	 */
	protected function getSortDirection( $sort ) {
		if( $sort->getDirection() === Sort::ASCENDING ) {
			return SORT_ASC;
		}
		return  SORT_DESC;
	}

}
