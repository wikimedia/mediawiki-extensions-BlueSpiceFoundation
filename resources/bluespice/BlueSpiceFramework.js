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

	init: function() {
		
	}
};

//Alias for BlueSpice
BsCore = BlueSpice;

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

});

window.selected_text = '';
