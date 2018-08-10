<?php

namespace BlueSpice\Html\OOUI;

use OOUI\InputWidget;

class MenuOptionWidget extends \OOUI\Widget {
	use \OOUI\LabelElement;

	protected $options = [];
	protected $handle = [];

	public function __construct( array $config = [] ) {
		// Parent constructor
		parent::__construct( $config );

		$this->initializeLabelElement( $config );

		$this->appendContent( $this->label );

		$this->addClasses( ["oo-ui-menuOptionWidget", "oo-ui-optionWidget", "oo-ui-decoratedOptionWidget" ] );
	}

	protected function getInputElement( $config ) {
		return null;
	}

	public function getConfig( &$config ) {
		return parent::getConfig( $config );
	}

	public function getJavaScriptClassName() {
		return 'OO.ui.MenuOptionWidget';
	}
}
