<?php

namespace BlueSpice\Html\OOUI;

trait FloatableElement {
	protected $floatable;
	protected $floatableContainer;
	protected $floatableWindow;

	protected $hideWhenOutOfView;
	protected $verticalPosition;
	protected $horizontalPosition;

	protected $validVerticalPositions = [ 'below', 'above', 'top', 'bottom', 'center' ];
	protected $validHorizontalPositions = [ 'before', 'after', 'start', 'end', 'center' ];

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
		if( isset( $config[ 'hideWhenOutOfView' ] ) ) {
			$this->hideWhenOutOfView = (bool) $config['hideWhenOutOfView'];
		}
	}

	protected function setFloatableElement( $floatableElement ) {
		if( $this->floatable instanceof \OOUI\Tag ) {
			$this->floatable->removeClasses( [ 'oo-ui-floatableElement-floatable' ] );
		}

		$floatableElement->addClasses( [ 'oo-ui-floatableElement-floatable' ] );
		$this->floatable = $floatableElement;
	}

	protected function setVerticalPosition( $verticalPosition ) {
		if( in_array( $verticalPosition, $this->validVerticalPositions ) === false ) {
			return;
		}

		if( $this->verticalPosition !== $verticalPosition ) {
			$this->verticalPosition = $verticalPosition;
		}
	}

	protected function setHorizontalPosition( $horizontalPosition ) {
		if( in_array( $horizontalPosition, $this->validHorizontalPositions ) === false ) {
			return;
		}

		if( $this->horizontalPosition !== $horizontalPosition ) {
			$this->horizontalPosition = $horizontalPosition;
		}
	}

}
