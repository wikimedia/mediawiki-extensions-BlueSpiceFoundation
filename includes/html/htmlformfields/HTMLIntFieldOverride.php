<?php

class HTMLIntFieldOverride extends HTMLIntField {
	function validate( $value, $alldata ) {
		$p = parent::validate( $value, $alldata );

		if ( $p !== true ) return $p;

		if ( isset( $this->options['range_min'] ) && $value < $this->options['range_min'] ) {
			return $this->msg( 'htmlform-int-outofrange')->parseAsBlock();
		}

		if ( isset( $this->options['range_max'] ) && $value > $this->options['range_max'] ) {
			return $this->msg( 'htmlform-int-outofrange')->parseAsBlock();
		}

		return true;
	}

	function getInputOOUI( $value ) {
		$attr = [
			'value' => $value
		];
		if( isset( $this->mParams['min'] ) ) {
			$attr['min'] = $this->mParams['min'];
		}
		if( isset( $this->mParams['max'] ) ) {
			$attr['max'] = $this->mParams['max'];
		}
		if( isset( $this->mParams['step'] ) ) {
			$attr['step'] = $this->mParams['step'];
		}
		if( isset( $this->mParams['pageStep'] ) ) {
			$attr['pageStep'] = $this->mParams['pageStep'];
		}
		if( isset( $this->mParams['showButtons'] ) ) {
			$attr['showButtons'] = $this->mParams['showButtons'];
		}
		$attr['isInteger'] = true;
		// Compatibility layer - replace with \OOUI\NumberInputWidget
		// when MW core requires oojs/oojs-ui at least v0.27.0
		return new \BlueSpice\Html\OOUI\NumberInputWidget( $attr );
	}
}
