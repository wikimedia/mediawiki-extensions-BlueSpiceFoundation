( function ( mw, bs, $, d,undefined ) {
	"use strict";

	//This allows us to place anchortags with special data attributes
	Ext.QuickTips.init();

	//Allows to have stateful ExtJS components
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider({
		expires: new Date(new Date().getTime() + (1000 * 60 * 60 * 24 * 30))
	}));

	//Experimental feature. May be improved in the future
	$('a.mw-userlink').each(function(){
		 Ext.create('Ext.tip.ToolTip', {
			title: $(this).data('bs-username'),
			target: this,
			anchor: 'right',
			/*autoLoad: {
				url: mw.util.wikiScript('api')
			},
			height: 200,*/
			width: 200,
			autoHide: false,
			closable: true,
			showDelay: 1000
		 });
	});

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
	//CRUDGridPanel defines flex:1 for all columns.
	//no need for flex:1 for the selModel Checkbox
	Ext.override(Ext.selection.CheckboxModel, {
		getHeaderConfig: function() {
			var obj = this.callParent(arguments);
			obj.flex = 0;
			return obj;
		},
	});

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