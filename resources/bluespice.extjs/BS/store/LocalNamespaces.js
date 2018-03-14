Ext.define( 'BS.store.LocalNamespaces', {
	extend: 'Ext.data.Store',
	fields: [ 'id', 'namespace' ],
	data: [],
	autoLoad: false,

	//Custom settings
	includeAll: false,
	excludeIds: [],

	constructor: function( config ){
		this.includeAll = config.includeAll || this.includeAll;
		this.excludeIds = config.excludeIds || this.excludeIds;
		config.data = config.data || this.getLocalNamespaces();
		this.callParent( [config] );
	},
	getLocalNamespaces: function() {
		var namespaces = [];

		if ( this.includeAll ) {
			namespaces.push( {
				id: -99,
				namespace: mw.message( 'bs-extjs-allns' ).plain()
			});
		}

		var aFormattedNamespaces = mw.config.get( "wgFormattedNamespaces" );
		for ( var id in aFormattedNamespaces ) {
			if( this.excludeIds.indexOf( +id ) !== -1 ) {
				continue;
			}
			var namespace = {};
			namespace.id = +id;
			if ( namespace.id === 0 ) {
				namespace.namespace = mw.message( 'bs-extjs-mainns' ).plain();
			} else {
				namespace.namespace = mw.config.get( "wgFormattedNamespaces" )[id];
			}

			namespaces.push( namespace );
		}

		return namespaces;
	}
});