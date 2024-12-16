BSPing = {
	interval: 0,
	aListeners: [],
	isInit: !!mw.config.get( 'bsgPingOnInit' ),

	init: function () {
		$( document ).triggerHandler( 'BSPingInit', [ BSPing ] );
		BSPing.interval = mw.config.get( 'bsgPingInterval' ) * 1000;
		if ( BSPing.interval < 1000 ) {
			return;
		}

		document.addEventListener( 'visibilitychange', function () {
			if ( BSPing.isTabActive() ) {
				this.isInit = !!mw.config.get( 'bsgPingOnInit' );
				BSPing.ping();
			} else {
				clearTimeout( BSPing.timeout );
			}
		} );

		if ( this.isTabActive() ) {
			BSPing.ping();
		}

	},
	ping: function () {
		if ( this.isInit ) {
			$( function () {
				this.doPing( BSPing.getDueListeners() );
			}.bind( this ) );
			this.isInit = false;
		} else {
			const aListenersToGo = BSPing.getDueListeners();
			if ( aListenersToGo.length > 0 ) {
				BSPing.doPing( aListenersToGo );
			} else {
				BSPing.timeout = setTimeout( BSPing.ping, BSPing.interval );
			}
		}
	},
	doPing: function ( listeners ) {
		// do this or getJSON will auto call given callbacks (would make BSPing totally freak out)
		const BsPingData = [];
		for ( let i = 0; i < listeners.length; i++ ) {
			BsPingData.push( {
				sRef: listeners[ i ].sRef,
				aData: listeners[ i ].aData
			} );
		}
		bs.api.tasks.execSilent( 'ping', 'ping', {
			iArticleID: mw.config.get( 'wgArticleId' ),
			sTitle: mw.config.get( 'wgTitle' ),
			iNamespace: mw.config.get( 'wgNamespaceNumber' ),
			iRevision: mw.config.get( 'wgCurRevisionId' ),
			BsPingData: BsPingData
		} ).done( BSPing.pingCallback( listeners ) );
	},
	isTabActive: function () {
		return !document.hidden;
	},
	registerListener: function ( sRef, iInterval, aData, callback ) {
		if ( typeof sRef === 'undefined' ) {
			return false;
		}

		const o = {
			sRef: sRef,
			iInterval: ( typeof iInterval === 'undefined' ? 10000 : iInterval ),
			aData: ( typeof aData === 'undefined' ? [] : aData ),
			callback: ( typeof callback === 'undefined' ? false : callback )
		};
		BSPing.aListeners.push( o );
		return true;
	},
	getDueListeners: function () {
		const aReturn = [];
		if ( BSPing.aListeners.length < 1 ) {
			return aReturn;
		}
		const currTMPListeners = [];
		for ( let i = 0; i < BSPing.aListeners.length; i++ ) {
			BSPing.aListeners[ i ].iInterval = ( BSPing.aListeners[ i ].iInterval - BSPing.interval );
			if ( !this.isInit && BSPing.aListeners[ i ].iInterval > 0 ) {
				currTMPListeners.push( BSPing.aListeners[ i ] );
				continue;
			}
			aReturn.push( BSPing.aListeners[ i ] );
		}

		BSPing.aListeners = currTMPListeners;

		return aReturn;
	},
	pingCallback: function ( aListenersToGo ) {
		return function ( result ) {
			if ( result.success !== true ) {
				return;
			}

			for ( let i = 0; i < aListenersToGo.length; i++ ) {
				if ( aListenersToGo[ i ].callback !== false && typeof ( aListenersToGo[ i ].callback ) === 'function' ) {
					const skip = false;
					$( document ).trigger( 'BSPingBeforeSingleCallback', [
						this,
						aListenersToGo[ i ].callback,
						result.payload[ aListenersToGo[ i ].sRef ],
						aListenersToGo[ i ],
						skip
					] );
					if ( skip ) {
						continue;
					}
					aListenersToGo[ i ].callback(
						result.payload[ aListenersToGo[ i ].sRef ],
						aListenersToGo[ i ]
					);
				}
			}

			BSPing.timeout = setTimeout( BSPing.ping, BSPing.interval );
		};
	}
};

BSPing.init();
