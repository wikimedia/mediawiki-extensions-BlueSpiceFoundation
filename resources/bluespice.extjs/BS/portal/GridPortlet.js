Ext.define('BS.portal.GridPortlet', {
	extend: 'BS.portal.Portlet',
	height: 300,

	initComponent: function(){

		//Some simple fixtures
		this.gdMainConfig = {
			store: Ext.create('Ext.data.ArrayStore', {
				fields: [
					{name: 'company'},
					{name: 'change', type: 'float'}
				],
				data: [
					['3m Co',                    71.72],
					['Alcoa Inc',                29.01],
					['Altria Group Inc',        -83.81],
					['American Express Company', 52.55]
				]
			}),
			columns: [{
				id :'company',
				text : 'Company',
				flex: 1,
				sortable : true,
				dataIndex: 'company'
			},{
				text   : 'Change',
				width    : 75,
				sortable : true,
				renderer : function(val) {
					var color = val > 0 ? 'green' : 'red';
					return '<span style="color:{0};">{1}</span>'.format(color, val);
				},
				dataIndex: 'change'
			}]
		};

		this.beforeInitComponent();

		this.gdMain = Ext.create('Ext.grid.Panel', {
			height: this.height,
			store: this.gdMainConfig.store,
			stripeRows: true,
			columnLines: true,
			columns: this.gdMainConfig.columns
		});

		this.items = [
			this.gdMain
		];

		this.afterInitComponent();

		this.callParent(arguments);
	}
});
