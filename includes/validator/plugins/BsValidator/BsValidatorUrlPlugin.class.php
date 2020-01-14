<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Mathias Scheer
 */

/**
 * DEPRECATED!
 * @deprecated since version 3.1 - Use ParamProcessor instead
 * called via BsValidatorPlugin::isValid('Url', 'http://example.com', $options)
 * $options can contain the key 'flags' that may hold a sum of the following flags:
 *     # FILTER_FLAG_PATH_REQUIRED
 *     # FILTER_FLAG_QUERY_REQUIRED
 */
class BsValidatorUrlPlugin implements BsValidatorPlugin {

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.1 - Use ParamProcessor instead
	 * @param mixed $validateThis the value to be checked against
	 * @param array $options
	 * @return BsValidatorResponse If type is not BsValidatorResponse an error is thrown
	 */
	public static function isValid( $validateThis, $options ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$params = ( array_key_exists( 'flags', $options ) && $options['flags'] != 0 )
			? $options['flags']
			: null;

		// PHP bug workaround (http://bugs.php.net/51192)
		if ( !filter_var( 'http://www.blue-spice.org/', FILTER_VALIDATE_URL ) ) {
			$validateThis = str_replace( '-', '_', $validateThis );
		}

		$result = ( $params === null )
			? filter_var( $validateThis, FILTER_VALIDATE_URL )
			// return is boolean
			: filter_var( $validateThis, FILTER_VALIDATE_URL, $params );

		return ( $result === false )
			? new BsValidatorResponse( 1, 'Validator', 'bs-validator-url-validation-not-approved' )
			: new BsValidatorResponse( 0, 'Validator', 'bs-validator-url-validation-approved' );
	}
}
