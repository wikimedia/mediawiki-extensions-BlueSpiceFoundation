( function ( mw, bs, $, d,undefined ) {
	"use strict";

	var basePath = mw.config.get('wgScriptPath') 
		+ '/extensions/BlueSpiceFoundation/resources/bluespice.extjs';

	Ext.Loader.setPath( 'BS',     basePath + '/BS');
	Ext.Loader.setPath( 'Ext.ux', basePath + '/Ext.ux');
	
	//Be nice to older browsers
	//HINT: http://stackoverflow.com/questions/2581302/globally-disable-ext-js-animations
	if( Ext.isIE9m ) {
		Ext.override(Ext.Window, {
			animShow: function(){
				this.afterShow();
			},
			animHide: function(){
				this.el.hide();
				this.afterHide();
			}
		});
	}

	/*
	//TODO: Find a way to have BS.Window and BS.Panel shorthands for 
	//mw.message.plain() and this.getId()+'-SubComponent'
	Ext.define('BS.mixins.MediaWiki', {
		mw: {
			msg: function( key ) {
				return this.message( key ).plain();
			},
			message: function( key ) {
				return mw.message( this.getId()+'-'+key);
			}
		}
	});
	*/

	function _newTitleStore() {
		return {};
	}

	function _newUserStore() {
		return {};
	}

	function _newCategoryTreeStore() {
		return {};
	}

	function _newLocalNamespacesStore( cfg ) {
		return Ext.create( 'BS.store.LocalNamespaces', cfg );
	}

	var extjs = {
		newTitleStore: _newTitleStore,
		newUserStore: _newUserStore,
		newCategoryTreeStore: _newCategoryTreeStore,
		newLocalNamespacesStore: _newLocalNamespacesStore
	};

	bs.extjs = extjs;
	//TODO: not nice... find better way.
	$(d).trigger( 'BSExtJSReady', [ bs.extjs ] );

}( mediaWiki, blueSpice, jQuery, document ) );
