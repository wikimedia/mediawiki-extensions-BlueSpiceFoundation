<?php

namespace BlueSpice\Html\OOUI;

trait FloatableElement {

	/** @var \OOUI\Element */
	protected $floatable;
	/** @var \OOUI\Element */
	protected $floatableContainer;
	/** @var \OOUI\Element */
	protected $floatableWindow;

	/** @var bool */
	protected $hideWhenOutOfView;
	/** @var string */
	protected $verticalPosition;
	/** @var string */
	protected $horizontalPosition;

	/** @var array */
	protected $validVerticalPositions = [ 'below', 'above', 'top', 'bottom', 'center' ];
	/** @var array */
	protected $validHorizontalPositions = [ 'before', 'after', 'start', 'end', 'center' ];

	/**
	 * @param array $config
	 */
	public function initializeFloatableElement( array $config = [] ) {
		$this->floatableContainer = $config[ 'floatableContainer' ];

		$floatableElement = isset( $config[ 'floatableElement' ] ) ?
			$config[ 'floatableElement' ] : new \OOUI\Tag();
		$this->setFloatableElement( $floatableElement );

		$verticalPosition = isset( $config[ 'verticalPosition' ] ) ?
			$config[ 'verticalPosition' ] : 'below';
		$this->setVerticalPosition( $verticalPosition );

		$horizontalPosition = isset( $config[ 'horizontalPosition' ] ) ?
			$config[ 'horizontalPosition' ] : 'start';
		$this->setHorizontalPosition( $horizontalPosition );

		$this->hideWhenOutOfView = true;
		if ( isset( $config[ 'hideWhenOutOfView' ] ) ) {
			$this->hideWhenOutOfView = (bool)$config['hideWhenOutOfView'];
		}
	}

	/**
	 * @param \OOUI\Element $floatableElement
	 */
	protected function setFloatableElement( $floatableElement ) {
		if ( $this->floatable instanceof \OOUI\Tag ) {
			$this->floatable->removeClasses( [ 'oo-ui-floatableElement-floatable' ] );
		}

		$floatableElement->addClasses( [ 'oo-ui-floatableElement-floatable' ] );
		$this->floatable = $floatableElement;
	}

	/**
	 * @param string $verticalPosition
	 * @return void
	 */
	protected function setVerticalPosition( $verticalPosition ) {
		if ( in_array( $verticalPosition, $this->validVerticalPositions ) === false ) {
			return;
		}

		if ( $this->verticalPosition !== $verticalPosition ) {
			$this->verticalPosition = $verticalPosition;
		}
	}

	/**
	 * @param string $horizontalPosition
	 * @return void
	 */
	protected function setHorizontalPosition( $horizontalPosition ) {
		if ( in_array( $horizontalPosition, $this->validHorizontalPositions ) === false ) {
			return;
		}

		if ( $this->horizontalPosition !== $horizontalPosition ) {
			$this->horizontalPosition = $horizontalPosition;
		}
	}

}
