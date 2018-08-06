<?php

namespace BlueSpice\Data;

class Record implements IRecord, \JsonSerializable {

	/**
	 *
	 * @var \stdClass
	 */
	protected $dataSet = null;

	/**
	 *
	 * @var \Status
	 */
	protected $status = null;

	/**
	 *
	 * @param \stdClass $dataSet
	 */
	public function __construct( $dataSet, \Status $status = null ) {
		$this->dataSet = $dataSet;
		$this->status = $status;
	}

	/**
	 *
	 * @param string $fieldName
	 * @param mixed|null $default
	 * @return mixed
	 */
	public function get( $fieldName, $default = null ) {
		if( isset( $this->dataSet->{$fieldName} ) ) {
			return $this->dataSet->{$fieldName};
		}
		return $default;
	}

	/**
	 *
	 * @param string $fieldName
	 * @param mixed $value
	 */
	public function set( $fieldName, $value ) {
		$this->dataSet->{$fieldName} = $value;
	}

	/**
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return (array)$this->dataSet;
	}

	/**
	 *
	 * @return \stdClass
	 */
	public function getData() {
		return $this->dataSet;
	}

	/**
	 *
	 * @return \Status
	 */
	public function getStatus() {
		if( $this->status ) {
			return $this->status;
		}
		$this->status = \Status::newGood();
		return $this->status;
	}

}
