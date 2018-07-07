<?php

class HTMLInfoFieldOverride extends HTMLInfoField {

	function getInputHTML( $value ) {
		return Xml::element( "a", array( 'href' => $value['href'] ), $value['content'] );
	}

}
