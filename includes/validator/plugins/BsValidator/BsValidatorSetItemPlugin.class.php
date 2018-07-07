<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Mathias Scheer
 */

// TODO MRG20100816: Kommentar
class BsValidatorSetItemPlugin implements BsValidatorPlugin {

	public static function isValid( $validateThis, $options ) {
		return ( !in_array( $validateThis , $options['set'] ) )
			? new BsValidatorResponse( 1, 'Validator', 'bs-validator-set-validation-not-approved', array( $options['setname'], implode( ',', $options['set'] ) ) )
			: new BsValidatorResponse( 0, 'Validator', 'bs-validator-set-validation-approved', array( $options['setname']) );
	}

}
