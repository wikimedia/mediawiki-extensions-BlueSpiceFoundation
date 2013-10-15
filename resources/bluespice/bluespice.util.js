(function(mw, bs, $, undefined) {
	"use strict";

	/*N-glton-pattern*/
	var alerts = {};
	var confirms = {};
	var prompts = {};

	function _prepareSimpleDialogWindowCfg(idPrefix, cfg) {
		cfg = cfg || {};
		return Ext.applyIf(cfg, {
			idPrefix: idPrefix,
			title: 'SimpleDialog',
			text: 'SimpleDialog Text'
		});
	}

	function _prepareSimpleDialogCallbackCfg(cfg) {
		cfg = cfg || {};
		return Ext.applyIf(cfg, {
			ok: function() {
			},
			cancel: function() {
			},
			scope: this
		});
	}

	/**
	 * Shows an ExtJS 4 alert window to the user
	 * @param {String} idPrefix: A {String} that allowes to identify the dialogs controls
	 * @param {Object} windowCfg: Allowes parameters "title" and "text" with type {String}
	 * @param {Object} callbackCfg: Allowes parameters "ok" with type {Function}
	 * @return {BS.AlertDialog}: The BS.AlertDialog instance
	 */
	function _alert(idPrefix, windowCfg, callbackCfg) {
		if (alerts[idPrefix])
			return alerts[idPrefix];
		
		if(!windowCfg.title && !windowCfg.titleMsg ) {
			windowCfg.titleMsg = 'bs-extjs-hint'
		}

		windowCfg = _prepareSimpleDialogWindowCfg(idPrefix, windowCfg);
		callbackCfg = _prepareSimpleDialogCallbackCfg(callbackCfg);

		var alertWindow = Ext.create('BS.AlertDialog', windowCfg);
		alertWindow.on('close', function() {
			alerts[idPrefix] = undefined
		}, this);
		alertWindow.on('ok', callbackCfg.ok, callbackCfg.scope);
		alertWindow.show();

		alerts[idPrefix] = alertWindow;
		return alertWindow;
	}

	function _confirm(idPrefix, windowCfg, callbackCfg) {
		if (confirms[idPrefix])
			return confirms[idPrefix];
		
		if(!windowCfg.title && !windowCfg.titleMsg ) {
			windowCfg.titleMsg = 'bs-extjs-confirm'
		}

		windowCfg = _prepareSimpleDialogWindowCfg(idPrefix, windowCfg);
		callbackCfg = _prepareSimpleDialogCallbackCfg(callbackCfg);

		var confirmWindow = Ext.create('BS.ConfirmDialog', windowCfg);
		confirmWindow.on('close', function() {
			confirms[idPrefix] = undefined
		}, this);
		confirmWindow.on('ok', callbackCfg.ok, callbackCfg.scope);
		confirmWindow.on('cancel', callbackCfg.cancel, callbackCfg.scope);
		confirmWindow.show();

		confirms[idPrefix] = confirmWindow;
		return confirmWindow;
	}

	function _prompt(idPrefix, windowCfg, callbackCfg) {
		if (prompts[idPrefix])
			return prompts[idPrefix];

		windowCfg = _prepareSimpleDialogWindowCfg(idPrefix, windowCfg);
		callbackCfg = _prepareSimpleDialogCallbackCfg(callbackCfg);

		var promptWindow = Ext.create('BS.PromptDialog', windowCfg);
		promptWindow.on('close', function() {
			prompts[idPrefix] = undefined
		}, this);
		promptWindow.on('ok', callbackCfg.ok, callbackCfg.scope);
		promptWindow.on('cancel', callbackCfg.cancel, callbackCfg.scope);
		promptWindow.show();

		prompts[idPrefix] = promptWindow;
		return promptWindow;
	}

	function _confirmNavigation(anchor) {
		return _confirm(
			'bs-confirm-link',
			{
				title: mw.message('bs-extjs-confirmNavigationTitle').plain(),
				text: mw.message('bs-extjs-confirmNavigationText').plain()
			},
		{
			ok: function() {
				window.location = anchor;
			}
		}
		);
	}

	function _getRemoteHandlerUrl(extension, method, params) {
		if (typeof(params) == 'undefined') {
			params = {};
		}
		var obj = {};
		if (typeof(params) == 'object') {
			obj = params;
		}
		else {
			obj = {};
			for (i in params) {
				obj[i] = params[i];
			}
		}
		obj.action = 'remote';
		obj.mod = extension;
		obj.rf = method;

		var querystring = $.param(obj);
		var script = mw.util.wikiScript();

		return [script, querystring].join('?');
	}

	function _getAjaxDispatcherUrl(rs, rsargs, sendCAIContext) {
		var script = mw.util.wikiScript();
		var params = {
			'action': 'ajax',
			'rs': rs
		}
		if (rsargs) {
			params.rsargs = rsargs;
		}
		var querystring = $.param(params);

		if (sendCAIContext) {
			//TODO: Maybe send JSON stringified as single param?
			querystring += "&" + $.param(
				this.getCAIContext()
				);
		}
		return script + "?" + querystring;
	}

	function _getCAIUrl(cainame, rsargs) {
		return this.getAjaxDispatcherUrl(['BsCommonAJAXInterface', cainame].join('::'), rsargs, true);
	}

	function _getCAIContext() {
		//HINT: http://www.mediawiki.org/wiki/Manual:Interface/JavaScript
		return {
			wgAction: mw.config.get('wgAction'),
			wgArticleId: mw.config.get('wgArticleId'),
			wgCanonicalNamespace: mw.config.get('wgCanonicalNamespace'),
			wgCanonicalSpecialPageName: mw.config.get('wgCanonicalSpecialPageName'),
			wgCurRevisionId: mw.config.get('wgCurRevisionId'),
			//wgIsArticle: mw.config.get('wgIsArticle'),
			wgNamespaceNumber: mw.config.get('wgNamespaceNumber'),
			wgPageName: mw.config.get('wgPageName'),
			wgRedirectedFrom: mw.config.get('wgRedirectedFrom'), //maybe null
			wgRelevantPageName: mw.config.get('wgRelevantPageName'),
			wgTitle: mw.config.get('wgTitle')
		};
	}

	function _getNamespaceText(nsId) {
		var formattedNamespaces = mw.config.get('wgFormattedNamespaces');
		return formattedNamespaces[nsId];
	}

	function _selection() {
		var _textbox,
			_selectedText = false,
			_origText,
			_startPos;

		this.autoSelection = '';
		
		this.reset = function() {
			_selectedText = false;
			_startPos = 0;
		};

		this.save = function() {
			if (_selectedText !== false) {
				return _selectedText;
			}

			var tempText, range, endPos;

			_textbox = document.getElementById('wpTextbox1');
			_textbox.focus();

			tempText = '';

			if (document.selection && document.selection.createRange) {
				if (this.autoSelection) {
					range = this.autoSelection;
				} else {
					range = document.selection.createRange();
				}

				_selectedText = range.text;
				tempText = _textbox.value;
				range.text = 'bs_selection';

				_origText = _textbox.value.replace(/\r\n/g, "\n");
				_startPos = origText.indexOf('bs_selection');
				_textbox.value = tempText;
			} else {
				_startPos = _textbox.selectionStart;
				endPos = _textbox.selectionEnd;

				_selectedText = _textbox.value.substring(_startPos, endPos);
				tempText = _textbox.value;
				_textbox.value = _textbox.value.substring(0, _startPos)
					+ 'bs_selection'
					+ _textbox.value.substring(endPos, _textbox.value.length);

				_origText = _textbox.value;
				_textbox.value = tempText;
			}

			return _selectedText;
		};

		this.restore = function(text, mode) {
			var tempText, pos, range;

			_textbox.focus();

			if (typeof(text) === 'undefined') {
				text = _selectedText;
			}
			if (mode === 'append') {
				tempText = _origText += text;
				_textbox.value = tempText.replace('bs_selection', '');
			} else {
				_textbox.value = _origText.replace('bs_selection', text);
			}

			_selectedText = false;

			if (_startPos >= 0) {
				if (mode === 'append') {
					pos = _startPos;
				} else {
					pos = _startPos + text.length;
				}
				if (document.selection && document.selection.createRange) {
					range = _textbox.createTextRange();
					range.move('character', pos);
					range.select();
				} else {
					_textbox.setSelectionRange(pos, pos);
				}
			}

			this.autoSelection = '';
		};
	}
	
	
	function _timestampToAgeString( unixTimestamp ) {
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

		//TODO: User mw.message() and user PLURAL-Features of jQueryMsg
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
	}
	
	/**
	 * Shows a message window
	 * @param {String} url The url providing the content for the window
	 * @param {String} title The title of the window
	 * @param {Int} width The width of the window
	 * @param {Int} height The height of the window
	 * @return {Void}
	 */
	function _toggleMessage( url, title, width, height ) {
		var win = Ext.create( 'Ext.Window', {
			id: 'winToggleMsg',
			autoLoad: url,
			width:width,
			title:title,
			closeAction: 'close'
		});
		win.show();
		return win;
	}
	
	/**
	 * Creates a new value object with all the properties of "obj" but prefixed 
	 * "data-bs-" to allow easy embedding in HTML elements
	 * @param {Object} obj 
	 * @return {Object}
	 */
	function _makeDataAttributeObject( obj ) {
		var data = {};
		for( var property in obj ) {
			data['data-bs-'+property] = obj[property];
		}
		return data;
	}
	
	/**
	 * Creates a new value object with all the properties of "obj" but without 
	 * "data-bs-" prefixes. Leaves unprefixed properties untouched. May 
	 * override unprefixed doublets.
	 * @param {Object} obj
	 * @return {Object}
	 */
	function _unprefixDataAttributeObject( obj ) {
		var data = {}, newProperty = '';
		for( var property in obj ) {
			newProperty = property;
			if (property.startsWith('data-bs-') !== false) {
				newProperty = property.substr(8, property.length);
			}
			data[newProperty] = obj[property];
		}
		return data;
	}
	
	/**
	 * Creates a new value object from a DOMNode object.
	 * @param {Object} node
	 * @return {Object}
	 */
	function _makeAttributeObject( node ) {
		var data = {}, attribute;
		for( var i = 0; i < node.attributes.length; i++ ) {
			attribute = node.attributes[i].name;
			data[attribute] = node.attributes[i].value;
		}
		return data;
	}

	var util = {
		getNamespaceText: _getNamespaceText,
		getRemoteHandlerUrl: _getRemoteHandlerUrl,
		getAjaxDispatcherUrl: _getAjaxDispatcherUrl,
		getCAIUrl: _getCAIUrl,
		getCAIContext: _getCAIContext,
		alert: _alert,
		confirm: _confirm,
		prompt: _prompt,
		confirmNavigation: _confirmNavigation,
		timestampToAgeString: _timestampToAgeString,
		toggleMessage: _toggleMessage,
		makeDataAttributeObject: _makeDataAttributeObject,
		unprefixDataAttributeObject: _unprefixDataAttributeObject,
		makeAttributeObject: _makeAttributeObject,
		selection: new _selection()
	};

	$(document).on('click', 'a.bs-confirm-nav', function(e) {
		e.preventDefault();

		bs.util.confirmNavigation(this);

		return false;
	});

	if (document.selection && document.selection.createRange) {
		$(document).on( 'mouseup', '#wpTextbox1', function() {
			util.selection.autoSelection = document.selection.createRange();
		})
		.on( 'keyup', '#wpTextbox1', function() {
			// IE also creates a selection if you are typing ... 
			// and you will get it as description in InsertLink -> not wanted 
			util.selection.autoSelection = '';
		});
	}
	
	//TODO: Find better place for this.
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

	bs.util = util;

}(mediaWiki, blueSpice, jQuery));
