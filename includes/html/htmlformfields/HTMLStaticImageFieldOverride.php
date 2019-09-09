<?php

class HTMLStaticImageFieldOverride extends HTMLInfoField {

	/**
	 *
	 * @param array $value
	 * @return string
	 */
	public function getInputHTML( $value ) {
		return Xml::element( "img", [ 'src' => $value['src'] ] );
	}

}
