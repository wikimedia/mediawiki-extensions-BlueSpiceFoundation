Ext.define('BS.form.SimpleSelectBox', {
	extend: 'Ext.form.field.ComboBox',
	queryMode: 'local',
	triggerAction: 'all',
	displayField: 'name',
	valueField: 'value',
	allowBlank: false,
	forceSelection: true,

	//Custom settings
	bsData: {},

	initComponent: function() {
		this.store = Ext.create('Ext.data.JsonStore', {
			fields: [ this.displayField, this.valueField ],
			data: this.bsData
		});
		this.callParent(arguments);
	}
});