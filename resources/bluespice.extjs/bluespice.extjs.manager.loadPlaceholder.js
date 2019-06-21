( function ( mw, bs, $, d,undefined ) {
	// DOM ready
	$( function() {
		var $manager, config, callback, observer, i;

		$manager = $( '.bs-manager-container' );

		// Detect rendering of ExtJS component in the container
		// this is done in effort to make loading indicator automatic
		// without the need for each manager extension to call popPending()
		if ( $manager.length === 0 ) {
			return;
		}
		if ( $manager.find( '.x-panel' ).length > 0 ) {
			// ExtJS component already rendered, stop loading right away
			// NOTE: This is a generic implementation, if extension is using some
			// non-standard components, its responsible for popping the loading indicator
			return $manager.find( '.placeholder-loader' ).remove();
		}
		// ExtJS component not rendered yet - watch for mutations
		config = {
			childList: true
		};
		callback = function( mutationList, observer ) {
			for ( i = 0; i < mutationList.length; i++ ) {
				if ( mutationList[i].type === 'childList' ) {
					$manager.find( '.placeholder-loader' ).remove();
				}
			}
		};
		observer = new MutationObserver( callback );
		observer.observe( $manager[0], config );
	} );
}( mediaWiki, blueSpice, jQuery, document ) );
