<?php

class HTMLStaticImageFieldOverride extends HTMLInfoField {

	function getInputHTML( $value ) {
		return Xml::element( "img", array( 'src' => $value['src'] ) );
	}

}
