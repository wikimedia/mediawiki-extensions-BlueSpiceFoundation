<?php

class BsValidatorMwNamespacePlugin implements BsValidatorPlugin {
	public static function isValid( $mwNamespace, $options ) {
		if ( strlen( $mwNamespace ) > 16 )
			return new BsValidatorResponse( 1, 'NamespaceManager', 'mw_ns_2long' );
		if ( $mwNamespace == '' )
			return new BsValidatorResponse( 2, 'NamespaceManager', 'mw_ns_is_empty' );
		if ( strpos( $mwNamespace, '\\' ) !== false )
			return new BsValidatorResponse( 3, 'NamespaceManager', 'mw_ns_contains_backslashes' );
		if ( strpos( $mwNamespace, '-' ) !== false )
			return new BsValidatorResponse( 4, 'NamespaceManager', 'mw_ns_contains_dashes' );
		if ( strpos( $mwNamespace, ' ' ) !== false )
			return new BsValidatorResponse( 5, 'NamespaceManager', 'mw_ns_contains_spaces' );
		if ($mwNamespace == null)
			return new BsValidatorResponse( 6, 'NamespaceManager', 'mw_ns_invalid' );

		//return new BsValidatorResponse(0, 'NamespaceManager', 'mw_ns_validation_approved');
		return new BsValidatorResponse(0);
	}
}
