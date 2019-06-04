<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Mathias Scheer
 */

// TODO MRG20100816: Kommentar

/**
 * DEPRECATED!
 * @deprecated since version 3.1 - Use ParamProcessor instead
 */
class BsValidatorPositiveIntegerPlugin implements BsValidatorPlugin {

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.1 - Use ParamProcessor instead
	 * @param mixed $validateThis the value to be checked against
	 * @param array $options
	 * @return BsValidatorResponse If type is not BsValidatorResponse an error is thrown
	 */
	public static function isValid( $validateThis, $options ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return ( !is_numeric( $validateThis) || $validateThis < 0 )
			? new BsValidatorResponse( 1, 'Validator', 'bs-validator-positive-integer-validation-not-approved' )
			: new BsValidatorResponse( 0, 'Validator', 'bs-validator-positive-integer-validation-approved' );
	}
}
