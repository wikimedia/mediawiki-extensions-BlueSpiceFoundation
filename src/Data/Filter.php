<?php

namespace BlueSpice\Data;

abstract class Filter {
	public const COMPARISON_EQUALS = 'eq';
	public const COMPARISON_NOT_EQUALS = 'neq';

	/**
	 * Prior in extjs 4. Default in all existing filters in BlueSpice
	 */
	public const COMPARISON_CONTAINS = 'ct';

	/**
	 * Since extjs 6. Will be transformed into 'ct'
	 */
	public const COMPARISON_LIKE = 'like';

	/**
	 * Prior in extjs 4. Default in all existing filters in BlueSpice
	 */
	public const KEY_FIELD = 'field';

	/**
	 * Since extjs 6. Will be transformed into 'field'
	 */
	public const KEY_PROPERTY = 'property';

	/**
	 * Prior in extjs 4. Default in all existing filters in BlueSpice
	 */
	public const KEY_COMPARISON = 'comparison';

	/**
	 * Since extjs 6. Will be transformed into 'comparison'
	 */
	public const KEY_OPERATOR = 'operator';

	public const KEY_TYPE = 'type';
	public const KEY_VALUE = 'value';

	/**
	 *
	 * @var string
	 */
	protected $field = '';

	/**
	 *
	 * @var mixed
	 */
	protected $value = null;

	/**
	 *
	 * @var string
	 */
	protected $comparison = '';

	/**
	 *
	 * @var bool
	 */
	protected $applied = false;

	/**
	 *
	 * @param array $params
	 */
	public function __construct( $params ) {
		$this->field = !isset( $params[static::KEY_FIELD] ) && isset( $params[static::KEY_PROPERTY] )
			? $params[static::KEY_PROPERTY]
			: $params[static::KEY_FIELD];
		$this->value = $params[static::KEY_VALUE];
		if ( isset( $params[ static::KEY_OPERATOR] ) ) {
			// compatibility. The comparison parameter changed into operator in
			// ExtJs 6
			$params[static::KEY_COMPARISON] = $params[ static::KEY_OPERATOR];
		}
		if ( !isset( $params[static::KEY_COMPARISON] ) ) {
			$params[static::KEY_COMPARISON] = static::COMPARISON_EQUALS;
		}
		if ( $params[static::KEY_COMPARISON] === static::COMPARISON_LIKE ) {
			// compatibility. The comparison 'ct' changed into like in ExtJs 6
			$params[static::KEY_COMPARISON] = static::COMPARISON_CONTAINS;
		}

		$this->comparison = $params[static::KEY_COMPARISON];
	}

	/**
	 *
	 * @return string
	 */
	public function getField() {
		return $this->field;
	}

	/**
	 *
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 *
	 * @return string
	 */
	public function getComparison() {
		return $this->comparison;
	}

	/**
	 *
	 * @param \BlueSpice\Data\Record $dataSet
	 * @return bool
	 */
	public function matches( $dataSet ) {
		if ( $this->applied ) {
			return true;
		}
		return $this->doesMatch( $dataSet );
	}

	/**
	 *
	 * @param bool $applied
	 */
	public function setApplied( $applied = true ) {
		$this->applied = $applied;
	}

	/**
	 *
	 * @param stdClass[]|array[] $filters
	 * @return Filter[]
	 */
	public static function newCollectionFromArray( $filters ) {
		$filterObjects = [];
		foreach ( $filters as $filter ) {
			if ( is_object( $filter ) ) {
				$filter = (array)$filter;
			}
			$filterObjects[] = static::makeFilter( $filter );
		}

		return $filterObjects;
	}

	/**
	 *
	 * @param stdClass|array $filter
	 * @return Filter
	 */
	protected static function makeFilter( $filter ) {
		return FilterFactory::newFromArray( $filter );
	}

	/**
	 * @param IRecord $dataSet
	 * @return bool
	 */
	abstract protected function doesMatch( $dataSet );
}
