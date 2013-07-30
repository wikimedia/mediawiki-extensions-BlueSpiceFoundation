/**
 * BlueSpice Clientside Framework
 *
 * @author     Sebastian Ulbricht
 * @author     Robert Vogel
 * @version    1.1.0
 * @version    $Id$
 * @copyright  Copyright (C) 2012 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * @class BlueSpice
 */
var BlueSpice = {
	ajax: false,
	selectedText: '',
	/**
	 * Searches for an element within an array. See PHP Manual for details.
	 * @param {Mixed} needle The element to be searched for
	 * @param {Array} haystack The array to be searched in
	 * @param {Bool} strict Wether or not to check for type
	 * @return {Mixed} The key of the element within the array of false if the needle was not found
	 */
	array_search: function( needle, haystack, strict ) {
		strict = !!strict;
		var key = '';

		for ( key in haystack ) {
			if ( ( strict && haystack[key] === needle ) || ( !strict && haystack[key] == needle ) ) {
				return key;
			}
		}
		return false;
	},

	// TODO MRG (21.09.10 14:46): toggle heisst, dass ich sie auch wieder ausschalten kann. das seh ich hier nicht.
	// ansonsten würde ich createMessage bevorzugen
	/**
	 * Shows a message window
	 * @param {String} url The url providing the content for the window
	 * @param {String} title The title of the window
	 * @param {Int} width The width of the window
	 * @param {Int} height The height of the window
	 * @return {Void}
	 */
	toggleMessage: function( url, title, width, height ) {
		var win = new Ext.Window({
			id: 'winToggleMsg',
			autoLoad: url,
			width:width,
			title:title,
			closeAction: 'close'
		});
		win.show();
	},

	/**
	 * Builds an url string to access the BlueSpice RemoteHandler modules
	 * @param {String} extension The name of the extension the module belongs to
	 * @param {String} method The method name to call
	 * @param {Mixed} params Optional GET params as String or object
	 * @return {String} The resulting url for a XHR call
	 */
	buildRemoteString: function( extension, method, params ) {
		return bs.util.getRemoteHandlerUrl( extension, method, params );
	},

	/**
	 * Opens an alert dialog
	 * @param {String} text The text in the alert box
	 * @param {String} alert_mode Possible modes are 'reload', 'ok' or an empty string
	 * @return {Void}
	 */
	alert: function( text, alert_mode ) {
		var res;
		//res = eval(text);
		alert( text );
		if ( alert_mode == 'reload' ) {
			window.location.reload( false );
		}
		return true;
		// TODO MRG (21.09.10 15:04): kann nich nicht mehrere alerts offen haben?
		if ( Ext.getCmp('winToggleMsg') ) Ext.getCmp('winToggleMsg').close();
		if ( typeof( res ) == 'object' ) {
			text = res[1];
		}

		Ext.Msg.alert( '', text, function() {
			if ( typeof( res ) == 'object' && res[0] == 'SUC' ) {
				// TODO MRG (21.09.10 15:05): die logik verstehe ich nicht. es kommt eine Meldung, und
				// wenn der mode 'reload' ist, dann wird nach OK neu geladen. ist das so umgesetzt?
				if( alert_mode == 'ok' || alert_mode == '' ) {
				//toggleMessage();
				}
				else if( alert_mode == 'reload' ) {
					window.location.reload( false );
				}
			}
			else if( alert_mode == 'reload' ) {
				window.location.reload( false );
			}
		} );
	},

	/**
	 * Returns the uri encoded value of an element
	 * @param {String} id The elements id
	 * @return {String} The uri encoded value
	 */
	getURIEncodedByID : function( id ) {
		return encodeURIComponent( document.getElementById(id).value );
	},

	/**
	 * Fetches data from url and displays it to the user
	 * @param {String} url the url to fetch
	 * @return {Void}
	 */
	requestWithAnswer: function( url ) {
		$.get(
			url, 
			function( data ) {
				BlueSpice.alert( data, 'ok' );
			});
	},

	/**
	 * Fetches data from url and displays it to the user. 
	 * Return value must consist of two parts divided by a semicolon: a) 'yes' or 'no' for reload, b) the message
	 * @param {String} url the url to fetch
	 * @return {Void}
	 */
	requestWithAnswerAndReload: function( url ) {
		$.get(
			url, 
			function( data ) {
				BlueSpice.alert(data, 'reload');
			});
	},

	/**
	 * Changes cursor to "wait" after one millisecond
	 * TODO MRG (21.09.10 15:07): geht diese methode? bisher hat das nicht so richtig geklappt. Wenn ja,
	 * dann müssen wir sie konsequent einsetzen. ODer bietet jQuery dafür eine Schnittstelle?
	 * @param {Bool} on true for "wait", false for "default"
	 * @return {Void}
	 */
	mouseWait: function( on ) {
		window.setTimeout( function() { 
			BlueSpice.mouseWaitTM( on )
		}, 1 );
	},

	/**
	 * Changes cursor to "wait"
	 * @param {Bool} on true for "wait", false for "default"
	 * @return {Void}
	 */
	mouseWaitTM: function( on ) {
		if ( on ) document.body.style.cursor = "wait";
		else document.body.style.cursor = "default";
	},

	/**
	 * Saves current scroll position
	 * @return {Void}
	 */
	saveScrollPosition: function() {
		if ( ( typeof( VisualEditorMode ) != "undefined" ) && VisualEditorMode ) return;

		if ( document.selection  && document.selection.createRange ) { // IE/Opera
			//save window scroll position
			if ( document.documentElement && document.documentElement.scrollTop )
				scroll_pos = document.documentElement.scrollTop
			else if ( document.body )
				scroll_pos = document.body.scrollTop;
		} else {
			var textbox = document.getElementById( 'wpTextbox1' );
			scroll_pos = textbox.scrollTop;
		}
	},

	/**
	 * Restores saved scroll position
	 * @return {Void}
	 */
	restoreScrollPosition: function() {
		if ( ( typeof( VisualEditorMode ) != "undefined" ) && VisualEditorMode ) return;
		if ( document.selection  && document.selection.createRange ) { // IE/Opera
			if ( document.documentElement && document.documentElement.scrollTop )
				document.documentElement.scrollTop = scroll_pos
			else if ( document.body )
				document.body.scrollTop = scroll_pos;
		} else {
			var textbox = document.getElementById( 'wpTextbox1' );
			textbox.scrollTop = scroll_pos;
		}
	},

	
	/**
	 * Saves current selection in 'wpTextbox1'
	 * @return {String} The selected text or empty string on error
	 */
	saveSelection: function() {
		if ( ( typeof( VisualEditorMode ) != "undefined" ) && VisualEditorMode ) return '';
		if ( typeof( VisualEditorMode ) != "undefined" ) selected_text = false;
		if ( selected_text !== false ) {
			//if (document.selection  && document.selection.createRange) document.selection.removeAllRanges();
			return selected_text;
		}

		var textbox = document.getElementById( 'wpTextbox1' );
		textbox.focus();
		var temp_text = '';
		if ( document.selection  && document.selection.createRange ) {
			var range = '';
			if ( BlueSpice.selectedText != '' ) {
				range = BlueSpice.selectedText;
			} else {
				range = document.selection.createRange();
			}
			selected_text = range.text;
			temp_text = textbox.value;
			range.text = "hw_selection";
			//start = textbox.value.indexOf("hw_selection");
			orig_text = textbox.value.replace(/\r\n/g, "\n");
			start_pos = orig_text.indexOf( "hw_selection" );
			textbox.value = temp_text;
		} else {
			start_pos = textbox.selectionStart;
			var endPos = textbox.selectionEnd;
			selected_text = textbox.value.substring( start_pos, endPos);
			temp_text = textbox.value;
			textbox.value = textbox.value.substring( 0, start_pos )
			+ "hw_selection"
			+ textbox.value.substring(endPos, textbox.value.length);
			orig_text = textbox.value;
			textbox.value = temp_text;
		}
		return selected_text;
	},

	/**
	 * Restores saved selection in 'wpTextbox1'
	 * @param {String} text The text to select
	 * @param {String} mode May be 'append' or ... empty string
	 * @return {Void}
	 */
	restoreSelection: function( text, mode ) {
		if ( ( typeof( VisualEditorMode ) != "undefined") && VisualEditorMode ) return;

		var textbox = document.getElementById( 'wpTextbox1' );
		textbox.focus();
		if ( text == undefined ) text = selected_text;
		if ( mode == 'append' ) {
			var tmptext = orig_text += text;
			textbox.value = tmptext.replace( "hw_selection", "" );
		}
		else
			textbox.value = orig_text.replace( "hw_selection", text );
		selected_text = false;

		if ( start_pos >= 0 ) {
			var pos = 0;
			if ( mode == 'append' ) pos = start_pos;
			else pos = start_pos + text.length;
			if ( document.selection && document.selection.createRange ) {
				var range = textbox.createTextRange();
				range.move( 'character', pos );
				range.select();
			} else {
				textbox.setSelectionRange( pos, pos );
			}
		}
		BlueSpice.selectedText = '';
	},

	/**
	 * Shows an input dialog and adds provided value to an ExtJS MulitSelect field
	 * @param {object} oSrc The ExtJS MulitSelect field
	 * @return {Void}
	 */
	addEntryToMultiSelect: function( oSrc ) {
		var sFieldName = oSrc.getAttribute( 'targetfield' ).substring(2);
		var sTitle = oSrc.getAttribute( 'title' );
		var sMessage = oSrc.getAttribute( 'msg' );
		Ext.Msg.prompt( sTitle, sMessage, function( btn, text ){
			if ( btn == 'ok' ){
				var oSelect = document.getElementById( 'mw-input-' + sFieldName );
				if(oSelect == null) {
					oSelect = document.getElementById( 'mw-input-' + 'wp' + sFieldName );
				}

				oSelect.options[oSelect.options.length] = new Option( text, text, false, false );
			}
		});
	},

	/**
	 * Removes an entry from an ExtJS MulitSelect field
	 * @param {object} oSrc The ExtJS MulitSelect field
	 * @return {Void}
	 */
	deleteEntryFromMultiSelect: function( oSrc ) {
		var sFieldName = oSrc.getAttribute( 'targetfield' ).substring(2);
		var elSel = document.getElementById( 'mw-input-' + sFieldName );
		if( elSel == null ) {
			elSel = document.getElementById( 'mw-input-' + 'wp' + sFieldName );
		}
		var i;
		for ( i = elSel.length - 1; i>=0; i-- ) {
			if ( elSel.options[i].selected ) {
				elSel.remove(i);
			}
		}
	},

	/**
	 * Blocks the UI
	 * @param {Int} iMilliseconds The time to block
	 * @return {Void}
	 */
	pause: function( iMilliseconds ) {
		var oStartDate   = new Date();
		var oCurrentDate = null;
		do {
			oCurrentDate = new Date();
		}
		while( oCurrentDate - oStartDate < iMilliseconds );
	},

	tempAnchor: null,
	/**
	 * Gets all GET parameters from an url.
	 * @param {Mixed} param [optional] The url to parse. May be a string, a anchor DOMElement or undefined. Default uses window.location.
	 * @return {Object}
	 */
	getUrlParams: function( param ) {
		// Handle BlueSpice::getUrlParams(), BlueSpice::getUrlParams(""), BlueSpice::getUrlParams(null), or BlueSpice::getUrlParams(undefined)
		if ( !param ) {
			return this._getUrlParams( window.location );
		}

		// Handle BlueSpice::getUrlParams(Anchor DOMElement)
		if ( param.nodeType ) {
			return this._getUrlParams( param );
		}

		// Handle string urls
		if ( typeof param === "string" ) {
			this.tempAnchor = document.createElement( 'a' );
			this.tempAnchor.href = param;
			return this._getUrlParams( this.tempAnchor );
		}

		return {};
	},

	// TODO RBV (31.07.12 15:11): Check for full browser compatibility as the location-Object has no official standard.
	_getUrlParams: function( loc ) {
		var oKeyValuePairs = {};
		if(loc.search == '') return oKeyValuePairs;
		var sParams = loc.search.substr(1);
		var aParams = sParams.split('&');

		for ( var i = 0; i < aParams.length; i++ ) {
			var aKeyValuePair = aParams[i].split('=');
			var key   = decodeURIComponent( aKeyValuePair[0] );
			var value = decodeURIComponent( aKeyValuePair[1] ); //With "?param1=val1&param2" oKeyValuePairs['param2'] will be "undefined". That's okay, but can be discussed.
			oKeyValuePairs[key] = value;
		}
		return oKeyValuePairs;
	},

	/**
	 * Gets a GET parameter from an url.
	 * @param {String} sParamName The requested parameters name
	 * @param {String} sDefaultValue [optional] A default value if the param is not available. Default ist an empty string.
	 * @param {Mixed} url [optional] The url to parse. May be a string, a anchor DOMElement or undefined. Default uses window.location.
	 * @return {String} The parameters value or the default value if parameter not set.
	 */
	getUrlParam: function( sParamName, sDefaultValue, url ) {
		var sValue = sDefaultValue || '';
		var oParams = this.getUrlParams( url );

		for( var key in oParams ) {
			if( key == sParamName ) sValue = oParams[key];
		}
		return sValue;
	},
	
	timestampToAgeString: function ( unixTimestamp ) {
		//This is a js version of "adapter/Utility/FormatConverter.class.php" -> timestampToAgeString
		//TODO: use PLURAL (probably wont work in mw 1.17)
		var start = (new Date(unixTimestamp));
		var now = (new Date());
		var diff = now - start;
		
		var sDateTimeOut = '';
		var sYears = '';
		var sMonths = '';
		var sWeeks = '';
		var sDays = '';
		var sHrs = '';
		var sMins = '';
		var sSecs = '';

		var sTsPast =  BsArticleInfo.lastEditTimestamp;
		var sTsNow = Math.round((new Date()).getTime() / 1000);
		var iDuration = sTsNow - sTsPast;

		var iYears=Math.floor(iDuration/(60*60*24*365)); iDuration%=60*60*24*365;
		var iMonths=Math.floor(iDuration/(60*60*24*30.5)); iDuration%=60*60*24*30.5;
		var iWeeks=Math.floor(iDuration/(60*60*24*7)); iDuration%=60*60*24*7;
		var iDays=Math.floor(iDuration/(60*60*24)); iDuration%=60*60*24;
		var iHrs=Math.floor(iDuration/(60*60)); iDuration%=60*60;
		var iMins=Math.floor(iDuration/60);
		var iSecs=iDuration%60;


		if (iYears == 1) { sYears = mw.msg('bs-year-duration', iYears); }
		if (iYears > 1) { sYears = mw.msg('bs-years-duration', iYears); }
		
		if (iMonths == 1) { sMonths = mw.msg('bs-month-duration', iMonths); }
		if (iMonths > 1) { sMonths = mw.msg('bs-months-duration', iMonths); }

		if (iWeeks == 1) { sWeeks = mw.msg('bs-week-duration', iWeeks); }
		if (iWeeks > 1) { sWeeks = mw.msg('bs-weeks-duration', iWeeks); }

		if (iDays == 1) { sDays = mw.msg('bs-day-duration', iDays); }
		if (iDays > 1) { sDays = mw.msg('bs-days-duration', iDays); }

		if (iHrs == 1) { sHrs = mw.msg('bs-hour-duration', iHrs); }
		if (iHrs > 1) { sHrs = mw.msg('bs-hours-duration', iHrs); }

		if (iMins == 1) { sMins = mw.msg('bs-min-duration', iMins); }
		if (iMins > 1) { sMins = mw.msg('bs-mins-duration', iMins); }

		if (iSecs == 1) { sSecs = mw.msg('bs-sec-duration', iSecs); }
		if (iSecs > 1) { sSecs = mw.msg('bs-secs-duration', iSecs); }

		if (iYears > 0) sDateTimeOut = sMonths ? mw.msg( 'bs-two-units-ago', sYears, sMonths) : mw.msg( 'bs-one-unit-ago', sYears);
		else if (iMonths > 0) sDateTimeOut = sWeeks ? mw.msg( 'bs-two-units-ago', sMonths, sWeeks) : mw.msg( 'bs-one-unit-ago', sMonths);
		else if (iWeeks > 0) sDateTimeOut = sDays ? mw.msg( 'bs-two-units-ago', sWeeks ,sDays) : mw.msg( 'bs-one-unit-ago', sWeeks)
		else if (iDays > 0) sDateTimeOut = sHrs ? mw.msg( 'bs-two-units-ago', sDays, sHrs) : mw.msg( 'bs-one-unit-ago', sDays);
		else if (iHrs > 0) sDateTimeOut = sMins ? mw.msg( 'bs-two-units-ago', sHrs, sMins) : mw.msg( 'bs-one-unit-ago', sHrs);
		else if (iMins > 0) sDateTimeOut = sSecs ? mw.msg( 'bs-two-units-ago', sMins, sSecs) : mw.msg( 'bs-one-unit-ago', sMins);
		else if (iSecs > 0) sDateTimeOut = mw.msg( 'bs-one-unit-ago', sSecs);
		else if (iSecs == 0) sDateTimeOut = mw.msg( 'bs-now' );
		
		return sDateTimeOut;
	},

	init: function() {
		$( '.multiselectsortlist' ).sortable( {
			update: function( event, ui ) { 
				$( this ).next().children().remove(); //Remove all "option" tags from the hidden "select" element
				$( this ).children().each( function( index, element ) {
					$( this ).parent().next() //The "select" element
					.append( '<option selected="selected" value="' + $(this).attr( 'data-value' ) + '">' + $(this).html() + '</option>' );
					//We have to use .attr( 'data-value' ) instead of .data('value' ) because of some jQuery version issues. Maybe correct this in future versions.
				});
			}
		});
	}
};

//Alias for BlueSpice
BsCore = BlueSpice;

BSPing = {
	interval: 0,
	aListeners:[],

	init: function() {
		this.interval = bsPingInterval*1000;
		if( this.interval < 1000 ) return;

		this.ping();
	},
	ping: function() {
		var aListenersToGo = this.calculateInterval();
		if( aListenersToGo.length < 1 ) {
			BSPing.timeout = setTimeout("BSPing.ping()", BSPing.interval);
			return;
		}

		//do this or getJSON will auto call given callbacks (would make BSPing totally freak out)
		var BsPingData = [];
		for( var i = 0; i < aListenersToGo.length; i++) {
			BsPingData.push({
				sRef:aListenersToGo[i].sRef,
				aData:aListenersToGo[i].aData
			});
		}

		$.post(
			wgScriptPath + '/index.php',
			{
				action:'ajax',
				rs:'BsAdapterMW::ajaxBSPing',
				iArticleID: wgArticleId,
				sTitle: wgTitle,
				iNamespace: wgNamespaceNumber,
				iRevision: wgCurRevisionId,
				BsPingData: BsPingData
			},
			this.pingCallback( aListenersToGo )
		);
	},
	registerListener: function( sRef, iInterval, aData, callback) {
		if( typeof sRef == "undefined") return false;

		var o = {
			sRef:		sRef,
			iInterval:	( typeof iInterval	== "undefined" ? 10000	: iInterval ),
			aData:		( typeof aData		== "undefined" ? []		: aData ),
			callback:	( typeof callback	== "undefined" ? false	: callback )
		}
		this.aListeners.push(o);
		return true;
	},
	calculateInterval: function() {
		var aReturn = [];
		if(BSPing.aListeners.length < 1) return aReturn;
		var currTMPListeners = [];

		for( var i = 0; i < BSPing.aListeners.length; i++) {
			BSPing.aListeners[i].iInterval = (BSPing.aListeners[i].iInterval - BSPing.interval);
			if( BSPing.aListeners[i].iInterval > 0 ) {
				currTMPListeners.push( BSPing.aListeners[i] );
				continue;
			}
			aReturn.push(BSPing.aListeners[i]);
		}

		BSPing.aListeners = currTMPListeners;

		return aReturn
	},
	pingCallback : function( aListenersToGo ) {
		return function( result ) {
			result = JSON.parse( result );
			if( result['success'] !== true) return;

			for( var i = 0; i < aListenersToGo.length; i++) {
				if(aListenersToGo[i].callback !== false && typeof(aListenersToGo[i].callback) == "function") {
					aListenersToGo[i].callback( result[aListenersToGo[i].sRef], aListenersToGo[i] );
				}
			}

			BSPing.timeout = setTimeout("BSPing.ping()", BSPing.interval);
		}
	}
}

mw.loader.using( 'ext.bluespice', function(){
	BsCore.init();
	//http://paulirish.com/2009/cornify-easter-egg-with-jquery/
	var kkeys = [];
	var konami = "38,38,40,40,37,39,37,39,66,65";

	$( document ).keydown( function( e ) {
		kkeys.push( e.keyCode );
		if ( kkeys.toString().indexOf( konami ) >= 0 ) {
			$( document ).unbind( 'keydown', arguments.callee );
			//TODO: Easteregg :)
		}
	});

	$( '#wpTextbox1' ).mouseup( function() {
		if ( document.selection && document.selection.createRange() ) {
			BlueSpice.selectedText = document.selection.createRange();
		}
	}).keyup( function() {
		if ( document.selection && document.selection.createRange() ) {
			// IE also creates a selection if you are typing ... and you will get it as description in insertlink -> not wanted 
			BlueSpice.selectedText = '';
		}
	});

	BSPing.init();
});