( function ( mw, bs, $, undefined ) {
	"use strict";
	
	/*N-glton-pattern*/
	var alerts   = {};
	var confirms = {};
	var prompts  = {};
	
	function prepareSimpleDialogWindowCfg( idPrefix, cfg ) {
		cfg = cfg || {};
		return Ext.applyIf( cfg, {
			idPrefix: idPrefix,
			title: 'SimpleDialog',
			text: 'SimpleDialog Text'
		});
	}
	
	function prepareSimpleDialogCallabckCfg( cfg ) {
		cfg = cfg || {};
		return Ext.applyIf( cfg, {
			ok: function() {},
			cancel: function() {},
			scope: this
		});
	}
	
	/**
	 * Shows an ExtJS 4 alter window to the user
	 * @param {String} idPrefix: A {String} that allowes to identify the dialogs controls
	 * @param {Object} windowCfg: Allowes parameters "title" and "text" with type {String}
	 * @param {Object} callbackCfg: Allowes parameters "ok" with type {Function}
	 * @return {BS.AlertDialog}: The BS.AlertDialog instance
	 */
	function alert( idPrefix, windowCfg, callbackCfg ) {
		if( alerts[idPrefix] ) return alerts[idPrefix];

		windowCfg   = prepareSimpleDialogWindowCfg( idPrefix, windowCfg );
		callbackCfg = prepareSimpleDialogCallabckCfg( callbackCfg );

		var alertWindow = Ext4.create( 'BS.AlertDialog', windowCfg );
		alertWindow.on('close', function() { alerts[idPrefix] = undefined }, this );
		alertWindow.on('ok', callbackCfg.ok, callbackCfg.scope );
		alertWindow.show();

		alerts[idPrefix] = alertWindow;
		return alertWindow;
	}
	
	function confirm( idPrefix, windowCfg, callbackCfg ) {
		if( confirms[idPrefix] ) return confirms[idPrefix];

		windowCfg   = prepareSimpleDialogWindowCfg( idPrefix, windowCfg );
		callbackCfg = prepareSimpleDialogCallabckCfg( callbackCfg );

		var confirmWindow = Ext4.create( 'BS.ConfirmDialog', windowCfg );
		confirmWindow.on('close', function() { confirms[idPrefix] = undefined }, this );
		confirmWindow.on('ok', callbackCfg.ok, callbackCfg.scope );
		confirmWindow.on('cancel', callbackCfg.cancel, callbackCfg.scope );
		confirmWindow.show();

		confirms[idPrefix] = confirmWindow;
		return confirmWindow;
	}
	
	function prompt( idPrefix, windowCfg, callbackCfg ) {
		if( prompts[idPrefix] ) return prompts[idPrefix];

		windowCfg   = prepareSimpleDialogWindowCfg( idPrefix, windowCfg );
		callbackCfg = prepareSimpleDialogCallabckCfg( callbackCfg );

		var promptWindow = Ext4.create( 'BS.PromptDialog', windowCfg );
		promptWindow.on('close', function() { prompts[idPrefix] = undefined }, this );
		promptWindow.on('ok', callbackCfg.ok, callbackCfg.scope );
		promptWindow.on('cancel', callbackCfg.cancel, callbackCfg.scope );
		promptWindow.show();

		prompts[idPrefix] = promptWindow;
		return promptWindow;
	}
	
	function getRemoteHandlerUrl( extension, method, params ) {
		if ( typeof( params ) == 'undefined' ) {
			params = {};
		}
		var obj = {};
		if ( typeof( params ) == 'object' ) {
			obj = params;
		}
		else {
			obj = {};
			for( i in params ) {
				obj[i] = params[i];
			}
		}
		obj.action = 'remote';
		obj.mod = extension;
		obj.rf = method;

		var querystring = $.param( obj );
		var script = mw.util.wikiScript();

		return [script, querystring].join('?');
	}
	
	function getAjaxDispatcherUrl( rs, rsargs, sendCAIContext ) {
		var script = mw.util.wikiScript();
		var querystring = $.param({
			'action': 'ajax',
			'rs':rs,
			'rsargs':rsargs
		});

		if( sendCAIContext ) {
			//TODO: Maybe send JSON stringified as single param?
			querystring += "&" + $.param(
				this.getCAIContext()
			);
		}
		return script+"?"+querystring;
	}
	
	function getCAIUrl( cainame, rsargs ) {
		return this.getAjaxDispatcherUrl( ['BSCommonAJAXInterface',cainame].join('::') , rsargs, true );
	}
	
	function getCAIContext() {
		//HINT: http://www.mediawiki.org/wiki/Manual:Interface/JavaScript
		return {
			action: mw.config.get('wgAction'),
			articleId:mw.config.get('wgArticleId'),
			canonicalNamespace: mw.config.get('wgCanonicalNamespace'),
			canonicalSpecialPageName: mw.config.get('wgCanonicalSpecialPageName'),
			curRevisionId: mw.config.get('wgCurRevisionId'),
			//isArticle: mw.config.get('wgIsArticle'),
			namespaceNumber: mw.config.get('wgNamespaceNumber'),
			pageName: mw.config.get('wgPageName'),
			redirectedFrom: mw.config.get('wgRedirectedFrom'), //maybe null
			relevantPageName: mw.config.get('wgRelevantPageName'),
			title: mw.config.get('wgTitle')
		};
	}
	
	var util = {
		getRemoteHandlerUrl: getRemoteHandlerUrl,
		getAjaxDispatcherUrl: getAjaxDispatcherUrl,
		getCAIUrl: getCAIUrl,
		getCAIContext: getCAIContext,
		
		alert: alert,
		confirm: confirm,
		prompt: prompt
	}
	
	bs.util = util;
	
}( mediaWiki, blueSpice, jQuery ) );
