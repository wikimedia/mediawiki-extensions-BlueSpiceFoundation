Ext.define('BS.portal.APIPortlet', {
	extend: 'BS.portal.Portlet',
	height: 300,
	bodyPadding: 5,

	//Custom Settings
	module: '',
	task: '',

	initComponent: function(){
		this.cContent = Ext.create('Ext.Component', {
			loader: {
				url: '',
				target: this.cContent,
				renderer: function( loader, response, active ) {
					var responseObj = Ext.JSON.decode( response.responseText );
					loader.getTarget().update( responseObj.payload.html );
					return true;
				}
			},
			autoScroll: true
		});

		this.loadContent();

		this.items = [
			this.cContent
		];
		this.on( 'configchange', this.onConfigChange, this);
		this.callParent(arguments);
	},
	onConfigChange: function( oConfig ) {
		this.loadContent();
	},
	loadContent: function() {
		var me = this;

		bs.api.tasks.execSilent(
			me.module, me.task, me.makeData()
		).done( function( response ) {
			me.cContent.update( response.payload.html );
		});
	},
	makeData: function() {
		return {};
	}
});