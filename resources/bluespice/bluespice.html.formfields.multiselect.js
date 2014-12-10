( function ( mw, bs, $, undefined ) {
	$( document ).on( 'submit', 'form', function() {
		$( this ).find( '.multiselectplusadd' ).each( function( index, item ) {
			for ( i = item.length - 1; i>=0; i-- ) {
				item.options[i].selected = true;
			}
		} );
	} );
}( mediaWiki, blueSpice, jQuery ) );