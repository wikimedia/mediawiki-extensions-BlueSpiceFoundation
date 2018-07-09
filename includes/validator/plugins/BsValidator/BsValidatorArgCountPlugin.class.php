<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Mathias Scheer
 */

// TODO MRG20100816: Ich habs zwar programmiert, aber wofür isses da?
class BsValidatorArgCountPlugin implements BsValidatorPlugin {

	public static function isValid( $validateThis, $options ) {
		return ( $validateThis < 0 )
			? new BsValidatorResponse( 1, 'Validator', 'bs-validator-arg-count-validation-not-approved' )
			: new BsValidatorResponse( 0, 'Validator', 'bs-validator-arg-count-validation-approved' );
	}

}
