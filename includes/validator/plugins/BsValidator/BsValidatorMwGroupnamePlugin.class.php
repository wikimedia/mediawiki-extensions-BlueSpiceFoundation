<?php

/**
 * DEPRECATED!
 * @deprecated since version 3.1 - Use ParamProcessor instead
 */
class BsValidatorMwGroupnamePlugin implements BsValidatorPlugin {

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.1 - Use ParamProcessor instead
	 * @param mixed $mwGroupname the value to be checked against
	 * @param array $options
	 * @return BsValidatorResponse If type is not BsValidatorResponse an error is thrown
	 */
	public static function isValid( $mwGroupname, $options ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		if ( strlen( $mwGroupname ) > 16 ) {
			return new BsValidatorResponse( 1, 'GroupManager', 'grp_2long' );
		}
		if ( $mwGroupname == '' ) {
			return new BsValidatorResponse( 2, 'GroupManager', 'no_grp' );
		}
		if ( strpos( $mwGroupname, '\\' ) !== false ) {
			return new BsValidatorResponse( 3, 'GroupManager', 'invalid_grp_esc' );
		}
		if ( strpos( $mwGroupname, ' ' ) !== false ) {
			return new BsValidatorResponse( 4, 'GroupManager', 'invalid_grp_spc' );
		}
		if ( $mwGroupname == null ) {
			return new BsValidatorResponse( 5, 'GroupManager', 'invalid_grp' );
		}

		// return new BsValidatorResponse(0, 'GroupManager', 'groupname_validation_approved');
		return new BsValidatorResponse( 0 );
	}
}
