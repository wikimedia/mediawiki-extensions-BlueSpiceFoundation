<?php

namespace BlueSpice\Html\OOUI;

use OOUI\InputWidget;

class SelectWidget extends \OOUI\Widget {
	use \OOUI\GroupElement;

	public function __construct( array $config = [] ) {
		// Parent constructor
		parent::__construct( $config );

		$groupConfig = $config;
		$groupConfig['group'] = $this;
		$this->initializeGroupElement( $groupConfig );

		$this->addClasses( ["oo-ui-selectWidget", "oo-ui-selectWidget-depressed" ] );
		$this->attributes['role'] = 'listbox';

		if( isset( $config[ 'items' ] ) && is_array( $config[ 'items' ] ) ) {
			$this->addItems( $config['items'] );
		}
		$this->appendContent( $this->group );
	}

	protected function getInputElement( $config ) {
		return null;
	}

	public function getConfig( &$config ) {
		return parent::getConfig( $config );
	}

	public function getJavaScriptClassName() {
		return "OO.ui.SelectWidget";
	}
}
