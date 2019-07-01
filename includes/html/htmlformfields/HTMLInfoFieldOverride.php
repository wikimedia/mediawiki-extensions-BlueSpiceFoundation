<?php

class HTMLInfoFieldOverride extends HTMLInfoField {

	function getInputHTML( $value ) {
		return Xml::element( "a", [ 'href' => $value['href'] ], $value['content'] );
	}

}
