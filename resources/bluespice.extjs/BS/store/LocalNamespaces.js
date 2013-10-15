Ext.define( 'BS.store.LocalNamespaces', {
	extend: 'Ext.data.Store',
	fields: [ 'id', 'namespace' ],
	data: [],
	autoLoad: false,
	
	//Custom settings
	includeAll: false,

	constructor: function( config ){
		this.data = this.getLocalNamespaces();
		this.callParent(arguments);
	},
	getLocalNamespaces: function() {
		var namespaces = [];

		if ( !this.includeAll ) {
			namespaces.push( {
				id: -99,
				namespace: mw.message( 'bs-extjs-allns' ).plain()
			});
		}

		for ( var id in wgFormattedNamespaces ) {
			var namespace = {};
			namespace.id = +id;
			if ( namespace.id === 0 ) {
				namespace.namespace = mw.message( 'blanknamespace' ).plain();
			} else {
				namespace.namespace = wgFormattedNamespaces[id];
			}

			namespaces.push( namespace );
		}

		return namespaces;
	}
});