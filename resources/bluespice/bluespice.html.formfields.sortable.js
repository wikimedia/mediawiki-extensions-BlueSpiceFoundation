( function ( mw, bs, $, undefined ) {
	mw.loader.using( 'jquery.ui.sortable' ).done( function () {
		$( '.multiselectsortlist' ).sortable( {
			update: function () {
				$( this ).next().children().remove(); //Remove all "option" tags from the hidden "select" element
				$( this ).children().each( function () {
					$( this ).parent().next() //The "select" element
					.append( '<option selected="selected" value="' + $( this ).attr( 'data-value' ) + '">' + $( this ).html() + '</option>' );
					//We have to use .attr( 'data-value' ) instead of .data('value' ) because of some jQuery version issues. Maybe correct this in future versions.
				});
			}
		});
	});
}( mediaWiki, blueSpice, jQuery ) );