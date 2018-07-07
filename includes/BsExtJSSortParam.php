<?php

class BsExtJSSortParam {
	protected $oData;

	public function __construct( $oData ) {
		$this->oData = $oData;
	}

	public function getProperty() {
		return $this->oData->property;
	}

	public function getDirection() {
		return strtoupper( $this->oData->direction );
	}

	public function __toString() {
		return $this->getProperty().' '.$this->getDirection();
	}
}
