<?php

namespace BlueSpice\Data;

class Sort {

	const ASCENDING = 'ASC';
	const DESCENDING = 'DESC';

	protected $property = '';

	protected $direction = '';

	/**
	 *
	 * @param string $property
	 * @param string $direction
	 * @throws UnexpectedValueException
	 */
	public function __construct( $property, $direction = self::ASCENDING ) {
		$this->property = $property;
		$this->direction = strtoupper( $direction );

		if( !in_array( $this->direction, [ self::ASCENDING, self::DESCENDING ] ) ) {
			throw new UnexpectedValueException(
				"'{$this->direction}' is not an allowed value for argument \$direction"
			);
		}
	}

	/**
	 *
	 * @return string
	 */
	public function getProperty() {
		return $this->property;
	}

	/**
	 *
	 * @return string One of Sort::ASCENDING or Sort::DESCENDING
	 */
	public function getDirection() {
		return $this->direction;
	}

	/**
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->getProperty().' '.$this->getDirection();
	}

		/**
	 *
	 * @param stdClass[]|array[] $sorts
	 * @return Sort[]
	 */
	public static function newCollectionFromArray( $sorts ) {
		$sortObjects = [];
		foreach( $sorts as $sort ) {
			if( is_array( $sort ) ) {
				$sort = (object) $sort;
			}

			$sortObjects[] = new Sort(
				$sort->property,
				isset( $sort->direction ) ? $sort->direction : null
			);
		}
		return $sortObjects;
	}
}
