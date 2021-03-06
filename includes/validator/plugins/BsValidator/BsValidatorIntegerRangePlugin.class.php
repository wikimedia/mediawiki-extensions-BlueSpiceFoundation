<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Mathias Scheer
 */

// TODO MRG20100816: Kommentar, auch Fehlercodes beschreiben

/**
 * DEPRECATED!
 * @deprecated since version 3.1 - Use ParamProcessor instead
 */
class BsValidatorIntegerRangePlugin implements BsValidatorPlugin {

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.1 - Use ParamProcessor instead
	 * @param mixed $validateThis the value to be checked against
	 * @param array $options
	 * @return BsValidatorResponse If type is not BsValidatorResponse an error is thrown
	 */
	public static function isValid( $validateThis, $options ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		if ( is_numeric( $validateThis ) ) {
			if ( isset( $options['lowerBoundary'] )
				&& ( $validateThis < $options['lowerBoundary'] ) ) {
				$response = new BsValidatorResponse(
					1,
					'Validator',
					'bs-validator-integer-range-validation-too-low',
					$options['lowerBoundary']
				);
			} elseif ( isset( $options['upperBoundary'] )
				&& ( $validateThis > $options['upperBoundary'] ) ) {
				$response = new BsValidatorResponse(
					2,
					'Validator',
					'bs-validator-integer-range-validation-too-high',
					$options['upperBoundary']
				);
			} else {
				$response = new BsValidatorResponse(
					0,
					'Validator',
					'bs-validator-integer-range-validation-approved'
				);
			}
		} else {
			$response = new BsValidatorResponse(
				3,
				'Validator',
				'bs-validator-integer-range-validation-no-integer'
			);
		}
		return $response;
	}
}
