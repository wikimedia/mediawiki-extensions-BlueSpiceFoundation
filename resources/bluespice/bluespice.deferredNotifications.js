( function ( mw, bs, $, undefined ) {

	// Helper function to get a unique identifier for a notification
	function _getNotificationId( message, options ) {
		return JSON.stringify( { message: message, options: options } );
	}

	function _push( message, options ) {
		options = options || {};
		const notifyInfo = localStorage.getItem( 'notify-info' );
		let notifyArray = [];
		if ( notifyInfo !== null ) {
			notifyArray = JSON.parse( notifyInfo );
		}

		notifyArray.push( [ message, options ] );
		localStorage.setItem( 'notify-info', JSON.stringify( notifyArray ) );
	}

	function _outputDeferredNotifications() {
		const notifyInfo = localStorage.getItem( 'notify-info' );
		const serverSideNotifications = mw.config.get( 'bsgDeferredNotifications', [] );
		let notifyArray = [];
		const shownNotifications = localStorage.getItem( 'shown-notifications' );
		let shownArray = [];

		// Load the already shown notifications
		if ( shownNotifications !== null ) {
			shownArray = JSON.parse( shownNotifications );
		}

		if ( notifyInfo !== null ) {
			notifyArray = JSON.parse( notifyInfo );
		}

		notifyArray = notifyArray.concat( serverSideNotifications );

		// Iterate and display notifications only if they haven't been shown before
		for ( let i = 0; i < notifyArray.length; i++ ) {
			const notificationId = _getNotificationId( notifyArray[ i ][ 0 ], notifyArray[ i ][ 1 ] );
			if ( shownArray.indexOf( notificationId ) === -1 ) {
				mw.notify( notifyArray[ i ][ 0 ], notifyArray[ i ][ 1 ] );
				shownArray.push( notificationId ); // Mark this notification as shown
			}
		}

		// Save the updated list of shown notifications
		localStorage.setItem( 'shown-notifications', JSON.stringify( shownArray ) );

		// Clear the deferred notifications from localStorage
		localStorage.removeItem( 'notify-info' );

		// Manage notification flag cookie
		$( '#wpLoginAttempt' ).on( 'click', function () {
			localStorage.removeItem( 'notificationFlagCookieSet' );
		} );

		// Used setTimeout() because mw.cookie.set needs sometime to set the cookie.
		setTimeout( function () {
			const cookieSet = localStorage.getItem( 'notificationFlagCookieSet' );
			if ( cookieSet != 1 ) {
				mw.cookie.set( 'notificationFlag', 1 );
			}
			const cookieValue = mw.cookie.get( 'notificationFlag' );
			if ( cookieValue === 1 ) {
				localStorage.setItem( 'notificationFlagCookieSet', 1 );
			}
		}, 100 );
	}

	$( _outputDeferredNotifications );

	bs.deferredNotifications = {
		push: _push
	};

}( mediaWiki, blueSpice, jQuery ) );
