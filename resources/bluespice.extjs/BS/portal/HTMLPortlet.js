Ext.define('BS.portal.HTMLPortlet', {
	extend: 'BS.portal.Portlet',
	height: 300,
	
	//Custom Settings
	contentUrl: mw.util.wikiScript('api'),

	initComponent: function(){
		this.cContent = Ext.create('Ext.Component', {
			loader: {
				url: this.contentUrl
			},
			autoScroll: true
		});
		//this.cContent.getLoader().on('beforeload', this.cContentBeforeLoad, this );
		//this.cContent.getLoader().on('load', this.cContentLoad, this );
		
		this.cContent.getLoader().load();
		
		this.items = [
			this.cContent
		];
		
		this.callParent(arguments);
	}/*,
	
	cContentBeforeLoad: function( loader, options, eOpts ) {
		this.setLoading(true);
	},
	
	cContentLoad: function( loader, response, options, eOpts ) {
		this.setLoading(false);
	}*/
});
