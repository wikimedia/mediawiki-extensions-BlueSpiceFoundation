<?php

class HTMLStaticImageFieldOverride extends HTMLInfoField {

	public function getInputHTML( $value ) {
		return Xml::element( "img", [ 'src' => $value['src'] ] );
	}

}
