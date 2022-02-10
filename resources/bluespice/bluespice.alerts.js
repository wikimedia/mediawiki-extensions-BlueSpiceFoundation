( function ( mw, $, bs, d, mwstake, undefined ) {
	bs.alerts = {
		add: mwstake.alerts.add,
		remove: mwstake.alerts.remove,

		// Keep in sync with IAlertProvider constants
		TYPE_SUCCESS: mwstake.alerts.TYPE_SUCCESS,
		TYPE_INFO: mwstake.alerts.TYPE_INFO,
		TYPE_WARNING: mwstake.alerts.TYPE_WARNING,
		TYPE_DANGER: mwstake.alerts.TYPE_DANGER
	};

	mw.hook( 'mwstake.components.alertbanners.alert.dismiss' ).add( function ( $alert, sender ) {
		mw.hook( 'bs.alert.dismiss' ).fire( $alert, sender );
	} );
}( mediaWiki, jQuery, blueSpice, document, mwstake ) );
