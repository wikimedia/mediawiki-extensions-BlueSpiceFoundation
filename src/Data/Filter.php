<?php

namespace BlueSpice\Data;

abstract class Filter {
	const COMPARISON_EQUALS = 'eq';
	const COMPARISON_NOT_EQUALS = 'neq';

	/**
	 * Prior in extjs 4. Default in all existing filters in BlueSpice
	 */
	const COMPARISON_CONTAINS = 'ct'; //

	/**
	 * Since extjs 6. Will be transformed into 'ct'
	 */
	const COMPARISON_LIKE = 'like'; //

	/**
	 * Prior in extjs 4. Default in all existing filters in BlueSpice
	 */
	const KEY_FIELD = 'field';

	/**
	 * Since extjs 6. Will be transformed into 'field'
	 */
	const KEY_PROPERTY = 'property';

	/**
	 * Prior in extjs 4. Default in all existing filters in BlueSpice
	 */
	const KEY_COMPARISON = 'comparison';

	/**
	 * Since extjs 6. Will be transformed into 'comparison'
	 */
	const KEY_OPERATOR = 'operator';

	const KEY_TYPE = 'type';
	const KEY_VALUE = 'value';

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
	 * @var boolean
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
		if( isset( $params[ static::KEY_OPERATOR] ) ) {
			//compatibility. The comparison parameter changed into operator in
			//ExtJs 6
			$params[static::KEY_COMPARISON] = $params[ static::KEY_OPERATOR];
		}
		if( !isset($params[static::KEY_COMPARISON] ) ) {
			$params[static::KEY_COMPARISON] = static::COMPARISON_EQUALS;
		}
		if( $params[static::KEY_COMPARISON] === static::COMPARISON_LIKE ) {
			//compatibility. The comparison 'ct' changed into like in ExtJs 6
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
	 * @return boolean
	 */
	public function matches( $dataSet ) {
		if( $this->applied ) {
			return true;
		}
		return $this->doesMatch( $dataSet );
	}

	/**
	 * !!TYPO IN FUNCTION NAME
	 * It is still here because there are a ton
	 * of usage in other extensions
	 * @param boolean $applied
	 * @deprecated since version 3.0.0 - use setApplied instead
	 */
	public function setAppied( $applied = true ) {
		$this->applied = $applied;
	}

	/**
	 *
	 * @param boolean $applied
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
		foreach( $filters as $filter ) {
			if( is_object(  $filter ) ) {
				$filter = (array) $filter;
			}
			$filterObjects[] = static::makeFilter( $filter );
		}

		return $filterObjects;
	}

	protected static function makeFilter( $filter ) {
		return FilterFactory::newFromArray( $filter );
	}

	protected abstract function doesMatch( $dataSet );
}
