<?php

namespace BlueSpice\Html\OOUI;

use OOUI\LabelElement;
use OOUI\Widget;

class MenuOptionWidget extends Widget {
	use LabelElement;

	protected $options = [];
	protected $handle = [];

	/**
	 *
	 * @param array $config
	 */
	public function __construct( array $config = [] ) {
		// Parent constructor
		parent::__construct( $config );

		$this->initializeLabelElement( $config );

		$this->appendContent( $this->label );

		$this->addClasses( [
			"oo-ui-menuOptionWidget",
			"oo-ui-optionWidget",
			"oo-ui-decoratedOptionWidget"
		] );
	}

	/**
	 *
	 * @param array $config
	 * @return null
	 */
	protected function getInputElement( $config ) {
		return null;
	}

	/**
	 *
	 * @param array &$config
	 * @return array
	 */
	public function getConfig( &$config ) {
		return parent::getConfig( $config );
	}

	/**
	 *
	 * @return string
	 */
	public function getJavaScriptClassName() {
		return 'OO.ui.MenuOptionWidget';
	}
}
