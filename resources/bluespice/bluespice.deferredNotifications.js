( function ( mw, bs, $, undefined ) {

	function _push( message, options ) {
		options = options || {};
		var notifyInfo = localStorage.getItem( 'notify-info' ),
			notifyArray = [];
		if ( notifyInfo !== null ) {
			notifyArray = JSON.parse( notifyInfo );
		}

		notifyArray.push( [ message, options ] );
		localStorage.setItem( 'notify-info', JSON.stringify( notifyArray ) );
	}

	function _outputDeferredNotifications() {
		var notifyInfo = localStorage.getItem( 'notify-info' ),
			serverSideNotifications = mw.config.get( 'bsgDeferredNotifications', [] ),
			notifyArray = [];
		if ( notifyInfo !== null ) {
			notifyArray = JSON.parse( notifyInfo );
		}

		notifyArray = notifyArray.concat( serverSideNotifications );

		for ( var i = 0; i < notifyArray.length; i++ ) {
			mw.notify( notifyArray[ i ][ 0 ], notifyArray[ i ][ 1 ] );
		}

		localStorage.removeItem( 'notify-info' );

		$( "#wpLoginAttempt" ).click( function() {
			localStorage.removeItem( 'notificationFlagCookieSet' );
		});

		// Used setTimeout() because mw.cookie.set needs sometime to set the cookie.
		setTimeout( function() {
			var cookieSet = localStorage.getItem( 'notificationFlagCookieSet' );
			if( cookieSet != 1 ) {
				mw.cookie.set( 'notificationFlag',1 );
			}
			var cookieValue = mw.cookie.get( 'notificationFlag' );
			if( cookieValue === 1) {
				localStorage.setItem( 'notificationFlagCookieSet',1 );
			}
		}, 100 );
	}

	$( _outputDeferredNotifications );

	bs.deferredNotifications = {
		push: _push
	};

}( mediaWiki, blueSpice, jQuery ) );
