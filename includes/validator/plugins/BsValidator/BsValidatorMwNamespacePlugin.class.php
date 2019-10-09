<?php

/**
 * DEPRECATED!
 * @deprecated since version 3.1 - Use ParamProcessor instead
 */
class BsValidatorMwNamespacePlugin implements BsValidatorPlugin {

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.1 - Use ParamProcessor instead
	 * @param mixed $mwNamespace the value to be checked against
	 * @param array $options
	 * @return BsValidatorResponse If type is not BsValidatorResponse an error is thrown
	 */
	public static function isValid( $mwNamespace, $options ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		if ( strlen( $mwNamespace ) > 16 ) {
			return new BsValidatorResponse( 1, 'NamespaceManager', 'mw_ns_2long' );
		}
		if ( $mwNamespace == '' ) {
			return new BsValidatorResponse( 2, 'NamespaceManager', 'mw_ns_is_empty' );
		}
		if ( strpos( $mwNamespace, '\\' ) !== false ) {
			return new BsValidatorResponse( 3, 'NamespaceManager', 'mw_ns_contains_backslashes' );
		}
		if ( strpos( $mwNamespace, '-' ) !== false ) {
			return new BsValidatorResponse( 4, 'NamespaceManager', 'mw_ns_contains_dashes' );
		}
		if ( strpos( $mwNamespace, ' ' ) !== false ) {
			return new BsValidatorResponse( 5, 'NamespaceManager', 'mw_ns_contains_spaces' );
		}
		if ( $mwNamespace == null ) {
			return new BsValidatorResponse( 6, 'NamespaceManager', 'mw_ns_invalid' );
		}

		// return new BsValidatorResponse(0, 'NamespaceManager', 'mw_ns_validation_approved');
		return new BsValidatorResponse( 0 );
	}
}
