<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Mathias Scheer
 */

// TODO MRG20100816: Kommentar
class BsValidatorPositiveIntegerPlugin implements BsValidatorPlugin
{
	public static function isValid( $validateThis, $options ) {
		return ( !is_numeric( $validateThis) || $validateThis < 0 )
			? new BsValidatorResponse( 1, 'Validator', 'bs-validator-positive-integer-validation-not-approved' )
			: new BsValidatorResponse( 0, 'Validator', 'bs-validator-positive-integer-validation-approved' );
	}
}
