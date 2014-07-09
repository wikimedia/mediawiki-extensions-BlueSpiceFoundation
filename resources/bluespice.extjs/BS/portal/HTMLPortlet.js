Ext.define('BS.portal.HTMLPortlet', {
	extend: 'BS.portal.Portlet',
	height: 300,
	bodyPadding: 5,

	//Custom Settings
	contentUrl: '',

	initComponent: function(){
		this.cContent = Ext.create('Ext.Component', {
			loader: {
				url: ''
			},
			autoScroll: true
		});
		//this.cContent.getLoader().on('beforeload', this.cContentBeforeLoad, this );
		//this.cContent.getLoader().on('load', this.cContentLoad, this );
		if ( this.contentUrl !== '' ) {
			this.cContent.getLoader().load({
				url: this.contentUrl
			});
		}

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