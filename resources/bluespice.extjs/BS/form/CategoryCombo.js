Ext.define( 'BS.form.CategoryCombo', {
	extend: 'Ext.form.field.ComboBox',
	requires: [ 'BS.model.Category' ],
	triggerAction: 'all',
	displayField: 'text',
	valueField: 'text',
	allowBlank: false,
	forceSelection: true,
	queryMode: 'local',
	emptyText: mw.message( "bs-extjs-combo-box-default-placeholder" ).plain(),

	initComponent: function() {
		this.store = this.makeStore();
		this.callParent(arguments);
	},

	makeStore: function() {
		var store = Ext.create( 'Ext.data.JsonStore', {
			proxy: {
				type: 'ajax',
				url: bs.api.makeUrl( 'bs-category-store' ),
				reader: {
					type: 'json',
					root: 'results',
					idProperty: 'cat_id'
				},
				extraParams: {
					limit: 9999
				}
			},
			model: 'BS.model.Category'
		});
		store.load();

		return store;
	},
});

