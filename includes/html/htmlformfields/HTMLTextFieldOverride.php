<?php

class HTMLTextFieldOverride extends HTMLTextField {

	function validate( $value, $alldata ) {
		$p = parent::validate( $value, $alldata );

		if ( $p !== true ) return $p;

		if ( isset( $this->options['range_min'] ) && $value < $this->options['range_min'] ) {
			return $this->msg( 'htmlform-text-outofrange')->parseAsBlock();
		}

		if ( isset( $this->options['range_max'] ) && $value > $this->options['range_max'] ) {
			return $this->msg( 'htmlform-text-outofrange' )->parseAsBlock();
		}

		return true;
	}

}
