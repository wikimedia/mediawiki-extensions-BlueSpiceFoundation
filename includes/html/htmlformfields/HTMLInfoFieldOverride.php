<?php

use MediaWiki\Xml\Xml;

class HTMLInfoFieldOverride extends HTMLInfoField {

	/**
	 *
	 * @param array $value
	 * @return string
	 */
	public function getInputHTML( $value ) {
		return Xml::element( "a", [ 'href' => $value['href'] ], $value['content'] );
	}

}
