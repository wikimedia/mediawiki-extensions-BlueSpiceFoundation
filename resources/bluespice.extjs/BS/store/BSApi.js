Ext.define('BS.store.BSApi', {
	extend: 'Ext.data.JsonStore',
	apiAction: null,

	constructor: function( cfg ) {
		cfg = Ext.merge({
			proxy: {
				type: 'ajax',
				url: mw.util.wikiScript('api'),
				extraParams: {
					format: 'json'
				},
				reader: {
					type: 'json',
					root: 'results',
					idProperty: 'id',
					totalProperty: 'total'
				}
			},
			autoLoad: true,
			remoteSort: true,
			sortInfo: {
				field: 'id',
				direction: 'ASC'
			}
		}, cfg);
		cfg.proxy.extraParams.action = cfg.apiAction;
		this.callParent([cfg]);
	}
});