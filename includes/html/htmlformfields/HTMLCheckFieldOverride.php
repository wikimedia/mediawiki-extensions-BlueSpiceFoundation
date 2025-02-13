<?php

use MediaWiki\HTMLForm\Field\HTMLCheckField;
use MediaWiki\Xml\Xml;

class HTMLCheckFieldOverride extends HTMLCheckField {

	/**
	 *
	 * @param bool $value
	 * @return string
	 */
	public function getInputHTML( $value ) {
		if ( !empty( $this->mParams['invert'] ) ) {
			$value = !$value;
		}

		$attr = $this->getTooltipAndAccessKey();
		$attr['id'] = $this->mID;
		if ( !empty( $this->mParams['disabled'] ) ) {
			$attr['disabled'] = 'disabled';
		}

		return Xml::check( $this->mName, $value, $attr );
	}

	/**
	 *
	 * @return string
	 */
	public function getLabel() {
		return $this->mLabel;
	}

}
