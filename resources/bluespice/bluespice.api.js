/*
 * Implementation for bs.api
 */

( function ( mw, bs, $, undefined ) {

	/**
	 * e.g. bs.api.tasks.execSilent(...).done(...);
	 * @param string module
	 * @param string taskname
	 * @param object data
	 * @returns jQuery.Promise
	 */
	function _execTaskSilent( module, task, data, cfg ) {
		cfg = cfg || {};
		cfg = $.extend( {
			success: function( response, module, task, $dfd, cfg ) {
				$dfd.resolve( response );
			},
			failure: function( response, module, task, $dfd, cfg ) {
				$dfd.resolve( response );
			}
		}, cfg );

		return _execTask( module, task, data, cfg );
	}
	/**
	 * e.g. bs.api.tasks.exec(
			'wikipage',
			'setCategories',
			{ categories: [ 'C1', 'C2' ] }
		)
		.done(...);
	 * @param string module
	 * @param string taskname
	 * @param object data
	 * @returns jQuery.Promise
	 */
	function _execTask( module, task, data, cfg ) {
		cfg = cfg || {};
		cfg = $.extend( {
			token: 'csrf',
			context: {},
			success: _msgSuccess,
			failure: _msgFailure
		}, cfg );

		var $dfd = $.Deferred();

		var api = new mw.Api();
		api.postWithToken( cfg.token, {
			action: 'bs-'+ module +'-tasks',
			task: task,
			taskData: JSON.stringify( data ),
			context: JSON.stringify(
				$.extend (
					_getContext(),
					cfg.context
				)
			)
		})
		.done(function( response ){
			if ( response.success === true ) {
				cfg.success( response, module, task, $dfd, cfg );
			} else {
				cfg.failure( response, module, task, $dfd, cfg );
			}
		})
		.fail( function( code, errResp ) { //Server error like FATAL
			var dummyResp = {
				success: false,
				message: errResp.exception,
				errors: [{
					message: code
				}]
			};
			cfg.failure( dummyResp, module, task, $dfd, cfg );
		});
		return $dfd.promise();
	}

	/**
	 * e.g. bs.api.store.getData(
			'groups'
		)
		.done(...);
	 * @param string module
	 * @param cfg data
	 * @returns jQuery.Promise
	 */
	function _getStoreData( module, cfg ) {
		cfg = cfg || {};
		cfg = $.extend( {
			token: 'csrf',
			context: {}
		}, cfg );

		var $dfd = $.Deferred();

		var api = new mw.Api();
		api.postWithToken( cfg.token, {
			action: 'bs-'+ module +'-store',
			context: JSON.stringify(
				$.extend (
					_getContext(),
					cfg.context
				)
			)
		})
		.done(function( response ){
			$dfd.resolve( response );
		})
		.fail( function( code, errResp ) { //Server error like FATAL
			$dfd.resolve( response );
		});
		return $dfd.promise();
	}

	function _msgSuccess( response, module, task, $dfd, cfg ) {
		if ( response.message.length ) {
			mw.notify( response.message, { title: mw.msg( 'bs-extjs-title-success' ) } );
			$dfd.resolve( response );
		}
		else {
			$dfd.resolve( response );
		}
	}

	function _msgFailure( response, module, task, $dfd, cfg ) {
		var message = response.message || '';
		if ( response.errors && response.errors.length > 0 ) {
			for ( var i in response.errors ) {
				if ( typeof( response.errors[i].message ) !== 'string' ) continue;
				message = message + '<br />' + response.errors[i].message;
			}
		}
		if ( message.length ) {
			bs.util.alert(
				module + '-' + task + '-fail',
				{
					titleMsg: 'bs-extjs-title-warning',
					text: message
				},
				{
					ok: function() {
						$dfd.reject( response );
					}
				}
			);
		}
		else {
			$dfd.reject( response );
		}
	}

	function _makeTaskUrl( module, task, data, additionalParams ) {

		var params = $.extend( {
			task: task,
			taskData: JSON.stringify( data ),
			token: mw.user.tokens.get( 'editToken' )
		}, additionalParams );

		return _makeUrl(
			'bs-'+ module +'-tasks',
			params,
			true
		);
	}

	function _makeUrl( action, params, sendContext ) {
		var baseParams = {
			'action': action
		};

		if ( sendContext ) {
			baseParams.context = JSON.stringify( _getContext() );
		}

		var script = mw.util.wikiScript( 'api' );
		var callParams = params || {};

		return script + "?" + $.param(
			$.extend( baseParams, callParams )
		);
	}

	function _getContext() {
		//HINT: https://www.mediawiki.org/wiki/Manual:Interface/JavaScript
		//Sync with serverside implementation of 'BSExtendedApiContext::newFromRequest'
		return {
			wgAction: mw.config.get( 'wgAction' ),
			wgArticleId: mw.config.get( 'wgArticleId' ),
			wgCanonicalNamespace: mw.config.get( 'wgCanonicalNamespace' ),
			wgCanonicalSpecialPageName: mw.config.get( 'wgCanonicalSpecialPageName' ),
			wgRevisionId: mw.config.get( 'wgRevisionId' ),
			//wgIsArticle: mw.config.get('wgIsArticle'),
			wgNamespaceNumber: mw.config.get( 'wgNamespaceNumber' ),
			wgPageName: mw.config.get( 'wgPageName' ),
			wgRedirectedFrom: mw.config.get( 'wgRedirectedFrom' ), //maybe null
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
		makeUrl: _makeUrl
	};

}( mediaWiki, blueSpice, jQuery ) );
