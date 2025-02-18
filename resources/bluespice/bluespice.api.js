/*
 * Implementation for bs.api
 */

( function ( mw, bs, $, undefined ) {

	/**
	 * e.g. bs.api.tasks.execSilent(...).done(...);
	 *
	 * @param {string} module
	 * @param {string} task
	 * @param {Object} data
	 * @param {Object} cfg
	 * @return {jQuery.Promise}
	 */
	function _execTaskSilent( module, task, data, cfg ) {
		cfg = cfg || {};
		cfg = $.extend( {
			success: function ( response, module, task, $dfd, cfg ) {
				$dfd.resolve( response );
			},
			failure: function ( response, module, task, $dfd, cfg ) {
				$dfd.resolve( response );
			},
			loadingIndicator: false
		}, cfg );

		return _execTask( module, task, data, cfg );
	}
	/**
	 * e.g. bs.api.tasks.exec(
	 * 'wikipage',
	 * 'setCategories',
	 * { categories: [ 'C1', 'C2' ] }
	 * )
	 * .done(...);
	 *
	 * @param {string} module
	 * @param {string} task
	 * @param {Object} data
	 * @param {Object} cfg - set { useService: true } to use new task service
	 * @return {jQuery.Promise}
	 */
	function _execTask( module, task, data, cfg ) {
		cfg = cfg || {};
		cfg = $.extend( {
			token: 'csrf',
			context: {},
			success: _msgSuccess,
			failure: _msgFailure,
			loadingIndicator: true
		}, cfg );

		const $dfd = $.Deferred();
		if ( cfg.loadingIndicator ) {
			bs.loadIndicator.pushPending();
		}

		const api = new mw.Api();
		api.postWithToken( cfg.token, {
			action: cfg.useService ? 'bs-task' : 'bs-' + module + '-tasks',
			task: task,
			taskData: JSON.stringify( data ),
			context: JSON.stringify(
				$.extend(
					_getContext(),
					cfg.context
				)
			)
		} )
			.done( function ( response ) {
				if ( cfg.loadingIndicator ) {
					bs.loadIndicator.popPending();
				}
				if ( response.success === true ) {
					cfg.success( response, module, task, $dfd, cfg );
				} else {
					cfg.failure( response, module, task, $dfd, cfg );
				}
			} )
			.fail( function ( code, result ) { // Server error like FATAL
				if ( cfg.loadingIndicator ) {
					bs.loadIndicator.popPending();
				}
				if ( result.exception ) {
					result = {
						success: false,
						message: result.exception,
						errors: [ {
							message: code
						} ]
					};
				}
				cfg.failure( result, module, task, $dfd, cfg );
			} );
		return $dfd.promise();
	}

	/**
	 * e.g. bs.api.store.getData(
	 * 'groups'
	 * )
	 * .done(...);
	 *
	 * @param {string} module
	 * @param {Object} cfg
	 * @return {jQuery.Promise}
	 */
	function _getStoreData( module, cfg ) {
		cfg = cfg || {};
		cfg = $.extend( {
			token: 'csrf',
			context: {},
			loadingIndicator: true
		}, cfg );

		const $dfd = $.Deferred();
		if ( cfg.loadingIndicator ) {
			bs.loadIndicator.pushPending();
		}

		const api = new mw.Api();
		api.postWithToken( cfg.token, {
			action: 'bs-' + module + '-store',
			context: JSON.stringify(
				$.extend(
					_getContext(),
					cfg.context
				)
			)
		} )
			.done( function ( response ) {
				if ( cfg.loadingIndicator ) {
					bs.loadIndicator.popPending();
				}
				$dfd.resolve( response );
			} )
			.fail( function ( code, errResp ) { // Server error like FATAL
				if ( cfg.loadingIndicator ) {
					bs.loadIndicator.popPending();
				}
				$dfd.resolve( errResp );
			} );
		return $dfd.promise();
	}

	function _configGet( values, context ) {
		context = context || {};

		if ( !Array.isArray( values ) ) {
			values = [ values ];
		}
		return _configRemote(
			values.join( '|' ),
			'get',
			context
		);
	}

	function _configHas( value ) {
		return _configRemote(
			value,
			'has'
		);
	}

	function _configRemote( value, func, context ) {
		const api = new mw.Api(),
			dfd = $.Deferred();

		api.get( {
			action: 'bs-js-var-config',
			func: func,
			name: value,
			context: JSON.stringify(
				$.extend(
					_getContext(),
					context
				)
			)
		} ).done( function ( response ) {
			if ( response.success && response.hasOwnProperty( 'payload' ) ) {
				dfd.resolve( response.payload );
				return;
			}
			const error = response.hasOwnProperty( 'error' ) ? response.error : '';
			dfd.reject( error );
		} ).fail( function ( error ) {
			dfd.reject( error );
		} );

		return dfd.promise();
	}

	function _msgSuccess( response, module, task, $dfd, cfg ) {
		if ( response.message.length ) {
			// Delay notification to ensure it is properly announced by screen readers
			// The confirmation dialog may prevent announcement, so a short delay resolves this
			setTimeout( () => {
				mw.notify( response.message, { title: mw.msg( 'bs-title-success' ) } );
			}, 500 );
			$dfd.resolve( response );
		} else {
			$dfd.resolve( response );
		}
	}

	function _msgFailure( response, module, task, $dfd, cfg ) {
		let message = response.message || '';
		if ( response.errors && response.errors.length > 0 ) {
			for ( const i in response.errors ) {
				if ( typeof ( response.errors[ i ].html ) === 'string' ) {
					message = message + '<br />' + response.errors[ i ].html;
					continue;
				}
				if ( typeof ( response.errors[ i ].plaintext ) === 'string' ) {
					message = message + '\n' + response.errors[ i ].plaintext;
					continue;
				}
				if ( typeof ( response.errors[ i ].wiki ) === 'string' ) {
					message = message + '\n*' + response.errors[ i ].wiki;
					continue;
				}
				if ( typeof ( response.errors[ i ].message ) === 'string' ) {
					message = message + '<br />' + response.errors[ i ].message;
					continue;
				}
				if ( typeof ( response.errors[ i ].code ) === 'string' ) {
					message = message + '<br />' + response.errors[ i ].code;
					continue;
				}
			}
		}
		if ( message.length ) {
			bs.util.alert(
				module + '-' + task + '-fail',
				{
					titleMsg: 'bs-title-warning',
					text: message
				},
				{
					ok: function () {
						$dfd.reject( response );
					}
				}
			);
		} else {
			$dfd.reject( response );
		}
	}

	function _makeTaskUrl( module, task, data, additionalParams ) {

		const params = $.extend( {
			task: task,
			taskData: JSON.stringify( data ),
			token: mw.user.tokens.get( 'csrfToken' )
		}, additionalParams );

		return _makeUrl(
			'bs-' + module + '-tasks',
			params,
			true
		);
	}

	function _makeUrl( action, params, sendContext ) {
		const baseParams = {
			action: action
		};

		if ( sendContext ) {
			baseParams.context = JSON.stringify( _getContext() );
		}

		const script = mw.util.wikiScript( 'api' ),
			callParams = params || {};

		return script + '?' + $.param(
			$.extend( baseParams, callParams )
		);
	}

	function _getContext() {
		// HINT: https://www.mediawiki.org/wiki/Manual:Interface/JavaScript
		// Sync with serverside implementation of 'BSExtendedApiContext::newFromRequest'
		return {
			wgAction: mw.config.get( 'wgAction' ),
			wgArticleId: mw.config.get( 'wgArticleId' ),
			wgCanonicalNamespace: mw.config.get( 'wgCanonicalNamespace' ),
			wgCanonicalSpecialPageName: mw.config.get( 'wgCanonicalSpecialPageName' ),
			wgRevisionId: mw.config.get( 'wgRevisionId' ),
			// wgIsArticle: mw.config.get('wgIsArticle'),
			wgNamespaceNumber: mw.config.get( 'wgNamespaceNumber' ),
			wgPageName: mw.config.get( 'wgPageName' ),
			wgRedirectedFrom: mw.config.get( 'wgRedirectedFrom' ), // maybe null
			wgRelevantPageName: mw.config.get( 'wgRelevantPageName' ),
			wgTitle: mw.config.get( 'wgTitle' )
		};
	}

	bs.api = {
		tasks: {
			exec: _execTask,
			execSilent: _execTaskSilent,
			makeUrl: _makeTaskUrl
		},
		store: {
			getData: _getStoreData
		},
		config: {
			get: _configGet,
			has: _configHas
		},
		makeUrl: _makeUrl
	};

}( mediaWiki, blueSpice, jQuery ) );
