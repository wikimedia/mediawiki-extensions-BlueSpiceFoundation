( function ( mw, bs, $, d, undefined ) {
	// DOM ready
	$( function () {
		var $cnt, config, callback, observer, i;

		$cnt = $( '.extjs-load-placeholder-cnt' );

		// Detect rendering of ExtJS component in the container
		// this is done in effort to make loading indicator automatic
		// without the need for each manager extension to call popPending()
		if ( $cnt.length === 0 ) {
			return;
		}
		if ( $cnt.find( '.x-panel' ).length > 0 ) {
			// ExtJS component already rendered, stop loading right away
			// NOTE: This is a generic implementation, if extension is using some
			// non-standard components, its responsible for popping the loading indicator
			return $cnt.find( '.placeholder-loader' ).remove();
		}
		// ExtJS component not rendered yet - watch for mutations
		config = {
			childList: true
		};
		callback = function ( mutationList, observer ) {
			for ( i = 0; i < mutationList.length; i++ ) {
				if ( mutationList[ i ].type === 'childList' ) {
					$cnt.find( '.placeholder-loader' ).remove();
				}
			}
		};
		observer = new MutationObserver( callback );
		observer.observe( $cnt[ 0 ], config );
	} );
}( mediaWiki, blueSpice, jQuery, document ) );
