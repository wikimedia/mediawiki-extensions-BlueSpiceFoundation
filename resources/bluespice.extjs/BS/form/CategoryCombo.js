Ext.define( 'BS.form.CategoryCombo', {
	extend: 'Ext.form.field.ComboBox',
	requires: [ 'BS.model.Category' ],
	triggerAction: 'all',
	displayField: 'text',
	valueField: 'text',
	allowBlank: false,
	forceSelection: true,
	emptyText: mw.message( "bs-extjs-combo-box-default-placeholder" ).plain(),

	initComponent: function() {
		this.store = this.makeStore();
		this.callParent(arguments);
	},

	makeStore: function() {
		var store = new BS.store.BSApi({
			apiAction: 'bs-category-store',
			model: 'BS.model.Category',
			autoLoad: true
		});

		return store;
	},
});

