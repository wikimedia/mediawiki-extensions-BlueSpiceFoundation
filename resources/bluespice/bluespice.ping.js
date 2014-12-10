BSPing = {
	interval: 0,
	aListeners:[],

	init: function() {
		$(document).triggerHandler('BSPingInit', [BSPing]);
		BSPing.interval = bsPingInterval*1000;
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

		$.post(
			wgScriptPath + '/index.php',
			{
				action:'ajax',
				rs:'BsCore::ajaxBSPing',
				iArticleID: wgArticleId,
				sTitle: wgTitle,
				iNamespace: wgNamespaceNumber,
				iRevision: wgCurRevisionId,
				BsPingData: BsPingData
			},
			BSPing.pingCallback( aListenersToGo )
		);
	},
	registerListener: function( sRef, iInterval, aData, callback) {
		if ( typeof sRef == "undefined") return false;

		var o = {
			sRef: sRef,
			iInterval: ( typeof iInterval == "undefined" ? 10000 : iInterval ),
			aData: ( typeof aData == "undefined" ? [] : aData ),
			callback: ( typeof callback == "undefined" ? false : callback )
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
			result = JSON.parse( result );
			if ( result.success !== true ) return;

			for ( var i = 0; i < aListenersToGo.length; i++) {
				if ( aListenersToGo[i].callback !== false && typeof(aListenersToGo[i].callback) == "function" ) {
					aListenersToGo[i].callback( result[aListenersToGo[i].sRef], aListenersToGo[i] );
				}
			}

			BSPing.timeout = setTimeout( BSPing.ping, BSPing.interval );
		};
	}
};

BSPing.init();