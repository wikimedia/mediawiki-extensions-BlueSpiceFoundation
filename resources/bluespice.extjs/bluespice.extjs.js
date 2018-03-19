( function ( mw, bs, $, d,undefined ) {
	var basePath = mw.config.get("wgExtensionAssetsPath") + '/BlueSpiceFoundation/resources/bluespice.extjs';

	Ext.BLANK_IMAGE_URL = mw.config.get( "wgScriptPath" ) + "/extensions/BlueSpiceFoundation/resources/bluespice.extjs/images/s.gif";
	Ext.Loader.setConfig({
		enabled: true,
		disableCaching: mw.config.get("debug")
	});
	Ext.Loader.setPath( 'BS', basePath + '/BS');
	var bsExtensionManagerAssetsPaths = mw.config.get( 'bsExtensionManagerAssetsPaths' );
	var extNamespace, unprefixedExtNamespace;
	for( var extName in bsExtensionManagerAssetsPaths ) {
		extNamespace = 'BS.' + extName;
		unprefixedExtNamespace = 'BS.' + extName.replace( /^BlueSpice/g, '' );
		Ext.Loader.setPath( extNamespace, bsExtensionManagerAssetsPaths[extName] + '/resources/' + extNamespace );
		Ext.Loader.setPath( unprefixedExtNamespace, bsExtensionManagerAssetsPaths[extName] + '/resources/' + unprefixedExtNamespace );
	}

	//This allows us to place elements with special data attributes
	Ext.QuickTips.init();

	//Allows to have stateful ExtJS components
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider({
		expires: new Date(new Date().getTime() + (1000 * 60 * 60 * 24 * 30))
	}));

	Ext.override(Ext.data.proxy.Server, {
		buildRequest: function(){
			this._lastRequest = this.callParent( arguments );
			return this._lastRequest;
		},
		_lastRequest: null,
		getLastRequest: function() {
			return this._lastRequest;
		}
	});

	//CRUDGridPanel defines flex:1 for all columns.
	//no need for flex:1 for the selModel Checkbox
	Ext.override(Ext.selection.CheckboxModel, {
		getHeaderConfig: function() {
			var obj = this.callParent(arguments);
			obj.flex = 0;
			return obj;
		}
	});

	function _newLocalNamespacesStore( cfg ) {
		return Ext.create( 'BS.store.LocalNamespaces', cfg );
	}

	var extjs = {
		newLocalNamespacesStore: _newLocalNamespacesStore
	};

	bs.extjs = extjs;
}( mediaWiki, blueSpice, jQuery, document ) );
