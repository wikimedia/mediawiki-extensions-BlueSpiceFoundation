<?php

class BSCategoryParser extends \ValueParsers\StringValueParser {
	/**
	 * @see StringValueParser::stringParse
	 *
	 * @param string $value
	 * @return Title
	 * @throws ParseException
	 */
	protected function stringParse( $value ) {
		if( strpos( $value, ':' ) !== false ) {
			$title = \Title::newFromText( $value );
			if( $title instanceof \Title === false || $title->getNamespace() !== NS_CATEGORY ) {
				throw new \ValueParsers\ParseException(
					wfMessage( 'bs-parser-error-invalid-category-title' , $value )->plain()
				);
			}
		} else {
			$title = \Title::newFromText( $value, NS_CATEGORY );
			if( $title instanceof \Title === false ) {
				throw new \ValueParsers\ParseException(
					wfMessage( 'bs-parser-error-invalid-title' , $value )->plain()
				);
			}
		}

		return $title;
	}
}
