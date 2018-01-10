Ext.define( 'BS.form.CategoryCombo', {
	extend: 'Ext.form.field.ComboBox',
	requires: [ 'BS.model.Category' ],
	triggerAction: 'all',
	displayField: 'text',
	valueField: 'text',
	allowBlank: false,
	forceSelection: true,

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

