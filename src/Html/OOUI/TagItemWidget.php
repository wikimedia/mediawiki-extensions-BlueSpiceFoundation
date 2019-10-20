<?php

namespace BlueSpice\Html\OOUI;

use OOUI\FlaggedElement;
use OOUI\LabelElement;
use OOUI\Widget;

class TagItemWidget extends Widget {
	use LabelElement;
	use FlaggedElement;

	protected $closeButton;

	protected $valid;
	protected $fixed;

	/**
	 *
	 * @param array $config
	 */
	public function __construct( array $config = [] ) {
		// Parent constructor
		parent::__construct( $config );

		$this->valid = !isset( $config['valid'] ) ? true : (bool)$config['valid'];
		$this->fixed = isset( $config['fixed'] ) ? (bool)$config['fixed'] : false;

		$this->initializeFlaggedElement( $config );
		$this->initializeLabelElement( $config );

		$this->closeButton = new \OOUI\ButtonWidget( [
			'framed' => false,
			'icon' => 'close',
			'tabIndex' => -1,
			'title' => wfMessage( 'ooui-item-remove' )
		] );
		$this->closeButton->setDisabled( $this->isDisabled() );

		$this->addClasses( [ "oo-ui-tagItemWidget" ] );
		$this->appendContent( $this->label, $this->closeButton );
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
		if ( $this->valid ) {
			$config['valid'] = $this->valid;
		}
		if ( $this->fixed ) {
			$config['fixed'] = $this->fixed;
		}
		return parent::getConfig( $config );
	}

	/**
	 *
	 * @return string
	 */
	public function getJavaScriptClassName() {
		return "OO.ui.TagItemWidget";
	}
}
