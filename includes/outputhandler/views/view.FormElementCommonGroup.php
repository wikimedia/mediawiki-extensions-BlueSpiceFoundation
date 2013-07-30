<?php

abstract class ViewFormElementCommonGroup extends ViewFormElementFieldset {

	public function __construct( $name ) {
		parent::__construct();
		$this->_mName = $name;
	}

	public function addItem( $item, $key = false ) {
		parent::addItem( $item, $key );
	}

	public function execute( $params = false ) {
		return parent::renderFieldset();
	}

}
