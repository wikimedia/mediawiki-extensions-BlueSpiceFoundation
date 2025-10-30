( function ( mw, bs, $, undefined ) {

	function _prepareSimpleDialogConfig( idPrefix, dialogConfig, actionCallback ) {
		cfg = dialogConfig || {};
		cfg.id = idPrefix;
		cfg.idPrefix = idPrefix;
		cfg.callback = actionCallback || {};
		if ( !cfg.callback.hasOwnProperty( 'ok' ) ) {
			cfg.callback.ok = function () {};
		}
		if ( !cfg.callback.hasOwnProperty( 'cancel' ) ) {
			cfg.callback.cancel = function () {};
		}
		return cfg;
	}

	/**
	 * Shows an OOJS alert window to the user
	 *
	 * @param {string} idPrefix A {String} that allowes to identify the dialogs controls
	 * @param {Object} windowCfg Allowes parameters "title" and "text" with type {String}
	 * @param {Object} callbackCfg Allowes parameters "ok" with type {Function}
	 * @return {BS.AlertDialog} The BS.AlertDialog instance
	 */
	function _alert( idPrefix, windowCfg, callbackCfg ) {
		if ( !windowCfg.title && !windowCfg.titleMsg ) {
			windowCfg.titleMsg = 'bs-extjs-hint';
		}

		const config = _prepareSimpleDialogConfig( idPrefix, windowCfg, callbackCfg );

		const dfd = new $.Deferred();
		mw.loader.using( 'ext.bluespice.oojs' ).done( function () {
			const windowManager = new OO.ui.WindowManager();

			const dialog = new bs.ui.dialog.AlertDialog( config );
			$( document.body ).append( windowManager.$element );

			windowManager.addWindows( [ dialog ] );
			windowManager.openWindow( dialog );

			dfd.resolve( dialog );
		} );

		return dfd.promise();
	}

	function _confirm( idPrefix, windowCfg, callbackCfg ) {
		if ( !windowCfg.title && !windowCfg.titleMsg ) {
			windowCfg.titleMsg = 'confirm';
		}

		const config = _prepareSimpleDialogConfig( idPrefix, windowCfg, callbackCfg );

		const dfd = new $.Deferred();
		mw.loader.using( 'ext.bluespice.oojs' ).done( function () {
			const windowManager = new OO.ui.WindowManager();

			const dialog = new bs.ui.dialog.ConfirmDialog( config );
			$( document.body ).append( windowManager.$element );

			windowManager.addWindows( [ dialog ] );
			windowManager.openWindow( dialog );

			dfd.resolve( dialog );
		} );

		return dfd.promise();
	}

	function _prompt( idPrefix, windowCfg, callbackCfg ) {
		const config = _prepareSimpleDialogConfig( idPrefix, windowCfg, callbackCfg );

		const dfd = new $.Deferred();
		mw.loader.using( 'ext.bluespice.oojs' ).done( function () {
			const windowManager = new OO.ui.WindowManager();

			const dialog = new bs.ui.dialog.PromptDialog( config );
			$( document.body ).append( windowManager.$element );

			windowManager.addWindows( [ dialog ] );
			windowManager.openWindow( dialog );

			dfd.resolve( dialog );
		} );

		return dfd.promise();
	}

	function _confirmNavigation( anchor ) {
		return _confirm(
			'bs-confirm-link',
			{
				title: mw.message( 'bs-extjs-confirmNavigationTitle' ).text(),
				text: mw.message( 'bs-extjs-confirmNavigationText' ).text()
			},
			{
				ok: function () {
					window.location = anchor;
				}
			}
		);
	}

	function _getRemoteHandlerUrl( extension, method, params ) {
		if ( typeof ( params ) === 'undefined' ) {
			params = {};
		}
		let obj = {};
		if ( typeof ( params ) === 'object' ) {
			obj = params;
		} else {
			let i;
			for ( i in params ) {
				obj[ i ] = params[ i ];
			}
		}
		obj.action = 'remote';
		obj.mod = extension;
		obj.rf = method;

		const querystring = $.param( obj ), script = mw.util.wikiScript();

		return [ script, querystring ].join( '?' );
	}

	function _getAjaxDispatcherUrl( rs, rsargs, sendCAIContext ) {
		const script = mw.util.wikiScript(),
			params = {
				action: 'ajax',
				rs: rs
			};
		if ( rsargs ) {
			params.rsargs = rsargs;
		}
		let querystring = $.param( params );

		if ( sendCAIContext ) {
			// TODO: Maybe send JSON stringified as single param?
			querystring += '&' + $.param(
				this.getCAIContext()
			);
		}
		return script + '?' + querystring;
	}

	function _getCAIUrl( cainame, rsargs ) {
		return this.getAjaxDispatcherUrl( [ 'BsCommonAJAXInterface', cainame ].join( '::' ), rsargs, true );
	}

	function _getCAIContext() {
		// HINT: https://www.mediawiki.org/wiki/Manual:Interface/JavaScript
		return {
			wgAction: mw.config.get( 'wgAction' ),
			wgArticleId: mw.config.get( 'wgArticleId' ),
			wgCanonicalNamespace: mw.config.get( 'wgCanonicalNamespace' ),
			wgCanonicalSpecialPageName: mw.config.get( 'wgCanonicalSpecialPageName' ),
			wgCurRevisionId: mw.config.get( 'wgCurRevisionId' ),
			// wgIsArticle: mw.config.get('wgIsArticle'),
			wgNamespaceNumber: mw.config.get( 'wgNamespaceNumber' ),
			wgPageName: mw.config.get( 'wgPageName' ),
			wgRedirectedFrom: mw.config.get( 'wgRedirectedFrom' ), // maybe null
			wgRelevantPageName: mw.config.get( 'wgRelevantPageName' ),
			wgTitle: mw.config.get( 'wgTitle' )
		};
	}

	function _getNamespaceText( nsId ) {
		const formattedNamespaces = mw.config.get( 'wgFormattedNamespaces' );
		return formattedNamespaces[ nsId ];
	}

	function _selection() {
		let _textbox,
			_selectedText = false,
			_origText,
			_startPos;

		this.autoSelection = '';

		this.reset = function () {
			_selectedText = false;
			_startPos = 0;
		};

		this.save = function () {
			if ( _selectedText !== false ) {
				return _selectedText;
			}

			let tempText, range, endPos;

			_textbox = document.getElementById( 'wpTextbox1' );
			_textbox.focus();

			tempText = '';

			if ( document.selection && document.selection.createRange ) {
				if ( this.autoSelection ) {
					range = this.autoSelection;
				} else {
					range = document.selection.createRange();
				}

				_selectedText = range.text;
				tempText = _textbox.value;
				range.text = 'bs_selection';

				_origText = _textbox.value.replace( /\r\n/g, '\n' );
				_startPos = _origText.indexOf( 'bs_selection' );
				_textbox.value = tempText;
			} else {
				_startPos = _textbox.selectionStart;
				endPos = _textbox.selectionEnd;

				_selectedText = _textbox.value.substring( _startPos, endPos );
				tempText = _textbox.value;
				_textbox.value = _textbox.value.slice( 0, Math.max( 0, _startPos ) ) +
					'bs_selection' +
					_textbox.value.substring( endPos, _textbox.value.length );

				_origText = _textbox.value;
				_textbox.value = tempText;
			}

			return _selectedText;
		};

		this.restore = function ( text, mode ) {
			let tempText, pos, range;

			_textbox.focus();

			if ( typeof ( text ) === 'undefined' ) {
				text = _selectedText;
			}
			if ( mode === 'append' ) {
				tempText = _origText += text;
				_textbox.value = tempText.replace( 'bs_selection', '' );
			} else {
				_textbox.value = _origText.replace( 'bs_selection', text );
			}

			_selectedText = false;

			if ( _startPos >= 0 ) {
				if ( mode === 'append' ) {
					pos = _startPos;
				} else {
					pos = _startPos + text.length;
				}
				if ( document.selection && document.selection.createRange ) {
					range = _textbox.createTextRange();
					range.move( 'character', pos );
					range.select();
				} else {
					_textbox.setSelectionRange( pos, pos );
				}
			}

			this.autoSelection = '';
		};
	}

	function _linker() {

		/**
		 * Example:
		 *
		 * linkDescs = {
		 * 'page1': { 'target': mw.Title.newFromText( 'Some page' ) },
		 * 'page2': { 'target': 'Some other page', 'text': 'Some label' },
		 * 'page3': { 'target': 'Some page 2', 'attribs': { 'class': 'pill' } },
		 * 'page4': { 'target': 'Some page 3', 'query': { 'action': 'history' }  },
		 * 'page5': { 'target': 'Invalid|Title' }
		 * };
		 *
		 * @param object linkDescs
		 * @param linkDescs
		 * @return {Promise}
		 */
		this.makeLinks = function ( linkDescs ) {
			const serializeableLinkDescs = {},
				dfd = new $.Deferred(),
				api = new mw.Api();

			$.each( linkDescs, function ( id, linkDesc ) {
				let targetText = linkDesc.target || '';

				// Convert {mw.Title} object
				if ( targetText.getPrefixedDb ) {
					targetText = targetText.getPrefixedDb();
				}

				if ( targetText === '' ) {
					return;
				}

				const serializeableLinkDesc = {
					target: targetText,
					text: linkDesc.text || null,
					attribs: linkDesc.attribs || {},
					query: linkDesc.query || {}
				};

				serializeableLinkDescs[ id ] = serializeableLinkDesc;
			} );

			api.get( {
				action: 'bs-linker',
				linkdescs: JSON.stringify( serializeableLinkDescs )
			} )
				.done( function ( result ) {
					dfd.resolve( result.links );
				} )
				.fail( dfd.reject );

			return dfd.promise();
		};

		/**
		 * @param {mw.Title|string} target
		 * @param {string} text
		 * @param {Object} attribs
		 * @param {Object} query
		 * @return {Promise}
		 */
		this.makeLink = function ( target, text, attribs, query ) {
			const dfd = new $.Deferred();
			this.makeLinks( {
				singlelink: {
					target: target,
					text: text,
					attribs: attribs,
					query: query
				}
			} )
				.done( function ( links ) {
					if ( links.singlelink ) {
						dfd.resolve( links.singlelink );
					} else {
						dfd.reject();
					}
				} );

			return dfd.promise();
		};
	}

	function _timestampToAgeString( unixTimestamp ) {
		// This is a js version of "adapter/Utility/FormatConverter.class.php" -> timestampToAgeString
		// TODO: use PLURAL (probably wont work in mw 1.17)
		let sDateTimeOut = '',
			sYears = '',
			sMonths = '',
			sWeeks = '',
			sDays = '',
			sHrs = '',
			sMins = '',
			sSecs = '';
		const sTsPast = unixTimestamp;
		const sTsNow = Math.round( Date.now() / 1000 );
		let iDuration = sTsNow - sTsPast;
		const iYears = Math.floor( iDuration / ( 60 * 60 * 24 * 365 ) );
		iDuration %= 60 * 60 * 24 * 365;
		const iMonths = Math.floor( iDuration / ( 60 * 60 * 24 * 30.5 ) );
		iDuration %= 60 * 60 * 24 * 30.5;
		const iWeeks = Math.floor( iDuration / ( 60 * 60 * 24 * 7 ) );
		iDuration %= 60 * 60 * 24 * 7;
		const iDays = Math.floor( iDuration / ( 60 * 60 * 24 ) );
		iDuration %= 60 * 60 * 24;
		const iHrs = Math.floor( iDuration / ( 60 * 60 ) );
		iDuration %= 60 * 60;
		const iMins = Math.floor( iDuration / 60 ),
			iSecs = iDuration % 60;

		if ( iYears > 0 ) {
			sYears = mw.message( 'bs-years-duration', iYears ).text();
		}
		if ( iMonths > 0 ) {
			sMonths = mw.message( 'bs-months-duration', iMonths ).text();
		}
		if ( iWeeks > 0 ) {
			sWeeks = mw.message( 'bs-weeks-duration', iWeeks ).text();
		}
		if ( iDays > 0 ) {
			sDays = mw.message( 'bs-days-duration', iDays ).text();
		}
		if ( iHrs > 0 ) {
			sHrs = mw.message( 'bs-hours-duration', iHrs ).text();
		}
		if ( iMins > 0 ) {
			sMins = mw.message( 'bs-mins-duration', iMins ).text();
		}
		if ( iSecs > 0 ) {
			sSecs = mw.message( 'bs-secs-duration', iSecs ).text();
		}

		if ( iYears > 0 ) {
			sDateTimeOut = sMonths ? mw.message( 'bs-two-units-ago', sYears, sMonths ).text() : mw.message( 'bs-one-unit-ago', sYears ).text();
		} else if ( iMonths > 0 ) {
			sDateTimeOut = sWeeks ? mw.message( 'bs-two-units-ago', sMonths, sWeeks ).text() : mw.message( 'bs-one-unit-ago', sMonths ).text();
		} else if ( iWeeks > 0 ) {
			sDateTimeOut = sDays ? mw.message( 'bs-two-units-ago', sWeeks, sDays ).text() : mw.message( 'bs-one-unit-ago', sWeeks ).text();
		} else if ( iDays > 0 ) {
			sDateTimeOut = sHrs ? mw.message( 'bs-two-units-ago', sDays, sHrs ).text() : mw.message( 'bs-one-unit-ago', sDays ).text();
		} else if ( iHrs > 0 ) {
			sDateTimeOut = sMins ? mw.message( 'bs-two-units-ago', sHrs, sMins ).text() : mw.message( 'bs-one-unit-ago', sHrs ).text();
		} else if ( iMins > 0 ) {
			sDateTimeOut = sSecs ? mw.message( 'bs-two-units-ago', sMins, sSecs ).text() : mw.message( 'bs-one-unit-ago', sMins ).text();
		} else if ( iSecs > 0 ) {
			sDateTimeOut = mw.message( 'bs-one-unit-ago', sSecs ).text();
		} else if ( iSecs === 0 ) {
			sDateTimeOut = mw.message( 'bs-now' ).text();
		}

		return sDateTimeOut;
	}

	function _convertDateToMWTimestamp( date ) {
		// welcome to handling timestamps in js
		const curr_year = date.getFullYear();
		let curr_month = date.getMonth() + 1;
		if ( curr_month < 10 ) {
			curr_month = '0' + curr_month;
		}
		let curr_date = date.getDate();
		if ( curr_date < 10 ) {
			curr_date = '0' + curr_date;
		}
		let curr_hour = date.getHours();
		if ( curr_hour < 10 ) {
			curr_hour = '0' + curr_hour;
		}
		let curr_min = date.getMinutes();
		if ( curr_min < 10 ) {
			curr_min = '0' + curr_min;
		}
		let curr_sec = date.getSeconds();
		if ( curr_sec < 10 ) {
			curr_sec = '0' + curr_sec;
		}
		const newtimestamp =
			curr_year.toString() +
			curr_month.toString() +
			curr_date.toString() +
			curr_hour.toString() +
			curr_min.toString() +
			curr_sec.toString();

		return newtimestamp.toString();
	}

	function _convertMWTimestampToISO( ts ) {
		// YYYYMMDDHHMMSS => YYYY-MM-DD HH:MM:SS
		return ts.replace(
			/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/g,
			'$1-$2-$3 $4:$5:$6'
		).toString();
	}

	function _convertISOToMWTimestamp( ts ) {
		// YYYY-MM-DDTHH:MM:SS => YYYYMMDDHHMMSS
		return ts.replace(
			/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/g,
			'$1$2$3$4$5$6'
		).toString();
	}

	function _convertMWTimestampToDate( ts ) {
		// ts = YYYYMMDDHHMMSS
		const tsSplit = ts.match( /^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/ ),
			year = parseInt( tsSplit[ 1 ] ),
			month = parseInt( tsSplit[ 2 ] ),
			day = parseInt( tsSplit[ 3 ] ),
			hour = parseInt( tsSplit[ 4 ] ),
			minute = parseInt( tsSplit[ 5 ] ),
			second = parseInt( tsSplit[ 6 ] );

		return new Date( year, month - 1, day, hour, minute, second );
	}

	/**
	 * Shows a message window
	 *
	 * @param {string} url The url providing the content for the window
	 * @param {string} title The title of the window
	 * @param {Int} width The width of the window
	 * @param {Int} height The height of the window
	 * @return {Void}
	 */
	function _toggleMessage( url, title, width, height ) {
		const win = Ext.create( 'Ext.Window', {
			id: 'winToggleMsg',
			autoLoad: url,
			width: width,
			title: title,
			closeAction: 'close'
		} );
		win.show();
		return win;
	}

	/**
	 * Creates a new value object with all the properties of "obj" but prefixed
	 * "data-bs-" to allow easy embedding in HTML elements
	 *
	 * @param {Object} obj
	 * @return {Object}
	 */
	function _makeDataAttributeObject( obj ) {
		const data = {};
		for ( const property in obj ) {
			data[ 'data-bs-' + property ] = obj[ property ];
		}
		return data;
	}

	/**
	 * Creates a new value object with all the properties of "obj" but without
	 * "data-bs-" prefixes. Leaves unprefixed properties untouched. May
	 * override unprefixed doublets.
	 *
	 * @param {Object} obj
	 * @return {Object}
	 */
	function _unprefixDataAttributeObject( obj ) {
		const data = {};
		let newProperty = '';
		for ( const property in obj ) {
			newProperty = property;
			if ( property.startsWith( 'data-bs-' ) !== false ) {
				newProperty = property.slice( 8, 8 + property.length );
			}
			data[ newProperty ] = obj[ property ];
		}
		return data;
	}

	/**
	 * Creates a new value object from a DOMNode object.
	 *
	 * @param {Object} node
	 * @return {Object}
	 */
	function _makeAttributeObject( node ) {
		const data = {};
		let attribute;
		for ( let i = 0; i < node.attributes.length; i++ ) {
			attribute = node.attributes[ i ].name;
			data[ attribute ] = node.attributes[ i ].value;
		}
		return data;
	}

	let _tempAnchor = null;

	/**
	 * Gets all GET parameters from an url.
	 *
	 * @param {Mixed} param [optional] The url to parse. May be a string, a anchor DOMElement or undefined. Default uses window.location.
	 * @return {Object}
	 */
	function _getUrlParams( param ) {
		// Handle getUrlParams(), getUrlParams(""), getUrlParams(null)
		// or getUrlParams(undefined) calls
		if ( !param ) {
			return _getUrlParams( window.location );
		}

		// Handle BlueSpice::getUrlParams(Anchor DOMElement)
		if ( param.nodeType ) {
			return _getUrlParams( param );
		}

		// Handle string urls
		if ( typeof param === 'string' ) {
			_tempAnchor = document.createElement( 'a' );
			_tempAnchor.href = param;
			return __getUrlParams( _tempAnchor );
		}

		return {};
	}

	// TODO RBV (31.07.12 15:11): Check for full browser compatibility as the location-Object has no official standard.
	function __getUrlParams( loc ) {
		const oKeyValuePairs = {};
		if ( loc.search === '' ) {
			return oKeyValuePairs;
		}
		const sParams = loc.search.slice( 1 ),
			aParams = sParams.split( '&' );

		for ( let i = 0; i < aParams.length; i++ ) {
			const aKeyValuePair = aParams[ i ].split( '=' ),
				key = decodeURIComponent( aKeyValuePair[ 0 ] ),
				value = decodeURIComponent( aKeyValuePair[ 1 ] ); // With "?param1=val1&param2" oKeyValuePairs['param2'] will be "undefined". That's okay, but can be discussed.
			oKeyValuePairs[ key ] = value;
		}
		return oKeyValuePairs;
	}

	/**
	 * Gets a GET parameter from an url.
	 *
	 * @param {string} sParamName The requested parameters name
	 * @param {string} sDefaultValue [optional] A default value if the param is not available. Default ist an empty string.
	 * @param {Mixed} url [optional] The url to parse. May be a string, a anchor DOMElement or undefined. Default uses window.location.
	 * @return {string} The parameters value or the default value if parameter not set.
	 */
	function _getUrlParam( sParamName, sDefaultValue, url ) {
		let sValue = sDefaultValue || '';
		const oParams = _getUrlParams( url );
		let key;

		for ( key in oParams ) {
			if ( key === sParamName ) {
				sValue = oParams[ key ];
			}
		}
		return sValue;
	}

	/**
	 * Shows an input dialog and adds provided value to an ExtJS MulitSelect field
	 *
	 * @param {Object} oSrc The ExtJS MulitSelect field
	 */
	function _addEntryToMultiSelect( oSrc ) {
		const sFieldName = oSrc.getAttribute( 'targetfield' ).slice( 2 ),
			sTitle = oSrc.getAttribute( 'title' ),
			sMessage = oSrc.getAttribute( 'msg' );
		Ext.Msg.prompt( sTitle, sMessage, function ( btn, text ) {
			if ( btn == 'ok' ) {
				let oSelect = document.getElementById( 'mw-input-' + sFieldName );
				if ( !oSelect ) {
					oSelect = document.getElementById( 'mw-input-' + 'wp' + sFieldName );
				}
				if ( !oSelect ) {
					oSelect = document.getElementsByName(
						oSrc.getAttribute( 'targetfield' )
					)[ 0 ];
				}
				if ( !oSelect ) {
					oSelect = document.getElementsByName(
						oSrc.getAttribute( 'targetfield' ) + '[]'
					)[ 0 ];
				}

				oSelect.options[ oSelect.options.length ] = new Option( text, text, false, true );
			}
		} );
	}

	/**
	 * Removes an entry from an ExtJS MulitSelect field
	 *
	 * @param {Object} oSrc The ExtJS MulitSelect field
	 */
	function _deleteEntryFromMultiSelect( oSrc ) {
		const sFieldName = oSrc.getAttribute( 'targetfield' ).slice( 2 );
		let elSel = document.getElementById( 'mw-input-' + sFieldName );
		if ( elSel === null ) {
			elSel = document.getElementById( 'mw-input-' + 'wp' + sFieldName );
		}
		let i;
		for ( i = elSel.length - 1; i >= 0; i-- ) {
			if ( elSel.options[ i ].selected ) {
				elSel.remove( i );
			}
		}
	}

	function _wikiGetlink( params, str ) {
		const pageName = str || mw.config.get( 'wgPageName' );
		params = params || {};
		params.title = pageName;

		const url = mw.util.wikiScript() + '?' + $.param( params );
		return url;
	}

	function _auditCssSelectors() {
		const links = [], rules = [], unmatched = [], selectors = { total: 0, matched: 0 };

		$.each( document.getElementsByTagName( 'link' ), function ( index, link ) {
			if ( link.sheet !== null ) {
				links.push( link.sheet );
			}
		} );

		$.each( links, function ( index, linkSheet ) {
			$.each( linkSheet.rules, function ( index, rule ) {
				selectors.total++;
				if ( document.querySelector( rule.selectorText ) !== null ) {
					selectors.matched++;
				} else {
					unmatched.push( rule.selectorText );
				}
			} );
		} );

		console.log( selectors.matched + ' / ' + selectors.total + ' = ' + selectors.matched / selectors.total );

		$.each( unmatched, function ( index, unmatched ) {
			console.log( unmatched );
		} );
	}

	/**
	 * This is a utility method that creates a object structure
	 * like window.mw.component.subcomponent.concretetype from a string
	 * like "mw.component.subcomponent.concretetype". This allows the creation
	 * of complex type structures with a single call. I.e. from the components
	 * sourcefile.
	 *
	 * @param string subNamespace
	 * @param object baseNamespace
	 * @param subNamespace
	 * @param baseNamespace
	 * @return {undefined}
	 */
	function _registerNamespace( subNamespace, baseNamespace ) {
		let baseNS = baseNamespace || window;
		const parts = subNamespace.split( '.' );
		const firstPart = parts[ 0 ];
		baseNS[ firstPart ] = baseNS[ firstPart ] || {};
		baseNS = baseNS[ parts[ 0 ] ];
		parts.shift(); // Remove first element
		if ( parts.length > 0 ) {
			_registerNamespace( parts.join( '.' ), baseNS );
		}
	}

	/**
	 * @param string callback e.G. "bs.extension.flyout.someKey"
	 * @param array args
	 * @param object scope
	 * @param callback
	 * @param args
	 * @param scope
	 * @param callback
	 * @param args
	 * @param scope
	 * @return mixed
	 */
	function _runCallback( callback, args, scope ) {
		const parts = callback.split( '.' );
		let func = window[ parts[ 0 ] ];
		for ( let i = 1; i < parts.length; i++ ) {
			func = func[ parts[ i ] ];
		}

		return func.apply( scope || null, args );
	}

	const util = {
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
		selection: new _selection(),
		getUrlParam: _getUrlParam,
		getUrlParams: _getUrlParams,
		addEntryToMultiSelect: _addEntryToMultiSelect,
		deleteEntryFromMultiSelect: _deleteEntryFromMultiSelect,
		wikiGetlink: _wikiGetlink,
		auditCssSelectors: _auditCssSelectors,
		registerNamespace: _registerNamespace,
		runCallback: _runCallback,
		convertDateToMWTimestamp: _convertDateToMWTimestamp,
		convertMWTimestampToISO: _convertMWTimestampToISO,
		convertISOToMWTimestamp: _convertISOToMWTimestamp,
		convertMWTimestampToDate: _convertMWTimestampToDate,
		linker: new _linker()
	};

	// This allows us to have a confirm dialog be displayed
	// by just adding a class to a link
	$( document ).on( 'click', 'a.bs-confirm-nav', function ( e ) {
		e.preventDefault();

		bs.util.confirmNavigation( this );

		return false;
	} );

	if ( document.selection && document.selection.createRange ) {
		$( document ).on( 'mouseup', '#wpTextbox1', function () {
			util.selection.autoSelection = document.selection.createRange();
		} )
			.on( 'keyup', '#wpTextbox1', function () {
			// IE also creates a selection if you are typing ...
			// and you will get it as description in InsertLink -> not wanted
				util.selection.autoSelection = '';
			} );
	}

	bs.util = util;

}( mediaWiki, blueSpice, jQuery ) );
