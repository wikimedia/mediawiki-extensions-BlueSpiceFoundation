<?php

class HTMLInfoFieldOverride extends HTMLInfoField {

	public function getInputHTML( $value ) {
		return Xml::element( "a", [ 'href' => $value['href'] ], $value['content'] );
	}

}
