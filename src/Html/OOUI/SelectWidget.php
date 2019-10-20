<?php

namespace BlueSpice\Html\OOUI;

use OOUI\GroupElement;
use OOUI\Widget;

class SelectWidget extends Widget {
	use GroupElement;

	/**
	 *
	 * @param array $config
	 */
	public function __construct( array $config = [] ) {
		// Parent constructor
		parent::__construct( $config );

		$groupConfig = $config;
		$groupConfig['group'] = $this;
		$this->initializeGroupElement( $groupConfig );

		$this->addClasses( [ "oo-ui-selectWidget", "oo-ui-selectWidget-depressed" ] );
		$this->attributes['role'] = 'listbox';

		if ( isset( $config[ 'items' ] ) && is_array( $config[ 'items' ] ) ) {
			$this->addItems( $config['items'] );
		}
		$this->appendContent( $this->group );
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
		return "OO.ui.SelectWidget";
	}
}
