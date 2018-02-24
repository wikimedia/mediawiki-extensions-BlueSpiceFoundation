<?php

class BsValidatorMwGroupnamePlugin implements BsValidatorPlugin {
	public static function isValid( $mwGroupname, $options ) {
		if ( strlen( $mwGroupname ) > 16 )
			return new BsValidatorResponse( 1, 'GroupManager', 'grp_2long' );
		if ( $mwGroupname == '' )
			return new BsValidatorResponse( 2, 'GroupManager', 'no_grp' );
		if ( strpos( $mwGroupname, '\\' ) !== false )
			return new BsValidatorResponse( 3, 'GroupManager', 'invalid_grp_esc' );
		if ( strpos( $mwGroupname, ' ' ) !== false )
			return new BsValidatorResponse( 4, 'GroupManager', 'invalid_grp_spc' );
		if ( $mwGroupname == null )
			return new BsValidatorResponse( 5, 'GroupManager', 'invalid_grp' );

		//return new BsValidatorResponse(0, 'GroupManager', 'groupname_validation_approved');
		return new BsValidatorResponse(0);
	}
}
