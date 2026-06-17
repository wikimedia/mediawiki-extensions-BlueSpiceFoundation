( function ( mw, bs, $ ) {
	$( document ).on( 'submit', 'form', function () {
		$( this ).find( '.multiselectplusadd' ).each( ( index, item ) => {
			for ( let i = item.length - 1; i >= 0; i-- ) {
				item.options[ i ].selected = true;
			}
		} );
	} );
}( mediaWiki, blueSpice, jQuery ) );
