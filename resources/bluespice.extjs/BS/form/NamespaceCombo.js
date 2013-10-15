Ext.define( 'BS.form.NamespaceCombo', {
	extend: 'Ext.form.ComboBox',
	displayField: 'namespace',
	labelAlign: 'right',
	valueField: 'id',
	queryMode: 'local',
	typeAhead: true,
	triggerAction: 'all',
	
	//Custom Settings
	includeAll: false,

	initComponent: function() {
		this.store = bs.extjs.newLocalNamespacesStore(
			{
				includeAll: this.includeAll
			}
		);

		this.callParent(arguments);
	}
});