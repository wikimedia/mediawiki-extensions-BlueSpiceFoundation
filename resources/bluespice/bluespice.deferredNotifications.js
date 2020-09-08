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
		mw.cookie.set( 'notificationFlag', 1 );
	}

	$( _outputDeferredNotifications );

	bs.deferredNotifications = {
		push: _push
	};

}( mediaWiki, blueSpice, jQuery ) );
