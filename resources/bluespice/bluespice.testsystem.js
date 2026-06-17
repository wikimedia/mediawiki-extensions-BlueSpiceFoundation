// Changes the dom so that the Testsystem is marked
( function ( mw, $ ) {
	$( () => {
		const bsgTestSystem = mw.config.get( 'bsgTestSystem' ),
			visualElement = $( // eslint-disable-line no-jquery/variable-pattern
				'<div class="bs-testsystem"><div><h3>' +
			bsgTestSystem.text +
			'</h3><h4 class="icon-warning"></h4></div></div>'
			);
		visualElement.insertBefore( '#bs-wrapper' );
		$( '.bs-testsystem' ).css( {
			'background-color': bsgTestSystem.color,
			height: '80px',
			'text-align': 'center'
		} );

		const navSection = $( '#bs-nav-sections' ); // eslint-disable-line no-jquery/variable-pattern
		if ( navSection.length > 0 ) {
			const navSectionTop = parseInt( navSection.css( 'top' ).replace( /px/g, '' ) ) + 80 + 'px';
			navSection.css( { top: navSectionTop } );
		}

		$( '.bs-testsystem' ).children().css( { margin: 'auto 0' } );
	} );
}( mediaWiki, jQuery ) );
