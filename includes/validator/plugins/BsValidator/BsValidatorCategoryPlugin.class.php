<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Mathias Scheer
 */

// TODO MRG20100816: Kommentar
class BsValidatorCategoryPlugin implements BsValidatorPlugin {

	public static function isValid( $validateThis, $options ) {
		// TODO: find a better filter
		return ( !is_string( $validateThis ) )
			? new BsValidatorResponse( 1, 'Validator', 'bs-validator-category-validation-not-approved' )
			: new BsValidatorResponse( 0, 'Validator', 'bs-validator-category-validation-approved' );
	}
}
