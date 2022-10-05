<?php

use OOUI\NumberInputWidget;

class HTMLIntFieldOverride extends HTMLIntField {
	/**
	 *
	 * @param int $value
	 * @param array $alldata
	 * @return bool
	 */
	public function validate( $value, $alldata ) {
		$p = parent::validate( $value, $alldata );

		if ( $p !== true ) {
			return $p;
		}

		if ( isset( $this->mOptions['range_min'] ) && $value < $this->mOptions['range_min'] ) {
			return $this->msg( 'htmlform-int-outofrange' )->parseAsBlock();
		}

		if ( isset( $this->mOptions['range_max'] ) && $value > $this->mOptions['range_max'] ) {
			return $this->msg( 'htmlform-int-outofrange' )->parseAsBlock();
		}

		return true;
	}

	/**
	 *
	 * @param int $value
	 * @return NumberInputWidget
	 */
	public function getInputOOUI( $value ) {
		$attr = [
			'value' => $value
		];
		if ( isset( $this->mParams['id'] ) ) {
			$attr['id'] = $this->mParams['id'];
		}
		if ( isset( $this->mParams['min'] ) ) {
			$attr['min'] = $this->mParams['min'];
		}
		if ( isset( $this->mParams['max'] ) ) {
			$attr['max'] = $this->mParams['max'];
		}
		if ( isset( $this->mParams['step'] ) ) {
			$attr['step'] = $this->mParams['step'];
		}
		if ( isset( $this->mParams['pageStep'] ) ) {
			$attr['pageStep'] = $this->mParams['pageStep'];
		}
		if ( isset( $this->mParams['showButtons'] ) ) {
			$attr['showButtons'] = $this->mParams['showButtons'];
		}
		return new NumberInputWidget( $attr );
	}
}
