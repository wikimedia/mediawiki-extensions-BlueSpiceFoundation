<?php

class HTMLCheckFieldOverride extends HTMLCheckField {

	function getInputHTML( $value ) {
		if ( !empty( $this->mParams['invert'] ) )
			$value = !$value;

		$attr = $this->getTooltipAndAccessKey();
		$attr['id'] = $this->mID;
		if( !empty( $this->mParams['disabled'] ) ) {
			$attr['disabled'] = 'disabled';
		}

		return Xml::check( $this->mName, $value, $attr );
	}

	function getLabel() {
		return $this->mLabel;
	}

}
