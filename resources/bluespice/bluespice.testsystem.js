//Changes the dom so that the Testsystem is marked
( function ( mw, $, bs, d, undefined ){
	$(function(){
		var bsgTestSystem = mw.config.get( 'bsgTestSystem' );
		var visualElement = $(
			'<div class="bs-testsystem"><div><h3>' +
			bsgTestSystem.text +
			'</h3><h4 class="icon-warning"></h4></div></div>'
		);
		visualElement.insertBefore("#bs-wrapper");
		$( '.bs-testsystem' ).css({
			"background-color": bsgTestSystem.color,
			"height":"80px",
			"text-align":"center"
		});

		var navSection = $( '#bs-nav-sections' );
		if( navSection.length > 0){
			var navSectionTop = parseInt( navSection.css( "top" ).replace( /px/g, "" ) ) + 80 + "px";
			navSection.css( { "top": navSectionTop } );
		}

		$( '.bs-testsystem' ).children().css( { "margin": "auto 0" } );
	});
})(mediaWiki,jQuery,blueSpice,document);
