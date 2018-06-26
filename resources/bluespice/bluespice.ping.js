/*global $, BSPing, mw */
/*jshint -W020 */
BSPing = {
	interval: 0,
	aListeners:[],

	init: function() {
		$(document).triggerHandler('BSPingInit', [BSPing]);
		BSPing.interval = mw.config.get( 'bsgPingInterval' ) * 1000;
		if ( BSPing.interval < 1000 ) return;

		BSPing.ping();
	},
	ping: function() {
		var aListenersToGo = BSPing.calculateInterval();
		if ( aListenersToGo.length < 1 ) {
			BSPing.timeout = setTimeout( BSPing.ping, BSPing.interval);
			return;
		}

		//do this or getJSON will auto call given callbacks (would make BSPing totally freak out)
		var BsPingData = [];
		for ( var i = 0; i < aListenersToGo.length; i++) {
			BsPingData.push({
				sRef:aListenersToGo[i].sRef,
				aData:aListenersToGo[i].aData
			});
		}
		bs.api.tasks.execSilent( 'ping', 'ping', {
				iArticleID: mw.config.get( "wgArticleId" ),
				sTitle: mw.config.get( "wgTitle" ),
				iNamespace: mw.config.get( "wgNamespaceNumber" ),
				iRevision: mw.config.get( "wgCurRevisionId" ),
				BsPingData: BsPingData
		}).done( BSPing.pingCallback( aListenersToGo ) );
	},
	registerListener: function( sRef, iInterval, aData, callback) {
		if ( typeof sRef === "undefined" ) {
			return false;
		}

		var o = {
			sRef: sRef,
			iInterval: ( typeof iInterval === "undefined" ? 10000 : iInterval ),
			aData: ( typeof aData === "undefined" ? [] : aData ),
			callback: ( typeof callback === "undefined" ? false : callback )
		};
		BSPing.aListeners.push(o);
		return true;
	},
	calculateInterval: function() {
		var aReturn = [];
		if ( BSPing.aListeners.length < 1 ) return aReturn;
		var currTMPListeners = [];

		for ( var i = 0; i < BSPing.aListeners.length; i++) {
			BSPing.aListeners[i].iInterval = (BSPing.aListeners[i].iInterval - BSPing.interval);
			if ( BSPing.aListeners[i].iInterval > 0 ) {
				currTMPListeners.push( BSPing.aListeners[i] );
				continue;
			}
			aReturn.push(BSPing.aListeners[i]);
		}

		BSPing.aListeners = currTMPListeners;

		return aReturn;
	},
	pingCallback : function( aListenersToGo ) {
		return function( result ) {
			if ( result.success !== true ) {
				return;
			}

			for ( var i = 0; i < aListenersToGo.length; i++) {
				if ( aListenersToGo[i].callback !== false && typeof(aListenersToGo[i].callback) === "function" ) {
					var skip = false;
					$(document).trigger('BSPingBeforeSingleCallback', [
						this,
						aListenersToGo[i].callback,
						result.payload[aListenersToGo[i].sRef],
						aListenersToGo[i],
						skip
					]);
					if ( skip ) {
						continue;
					}
					aListenersToGo[i].callback(
						result.payload[aListenersToGo[i].sRef],
						aListenersToGo[i]
					);
				}
			}

			BSPing.timeout = setTimeout( BSPing.ping, BSPing.interval );
		};
	}
};

BSPing.init();
