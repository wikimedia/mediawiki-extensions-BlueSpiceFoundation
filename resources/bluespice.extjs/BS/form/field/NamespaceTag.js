Ext.define( 'BS.form.field.NamespaceTag', {
	extend: 'Ext.form.field.Tag',
	displayField: 'namespace',
	labelAlign: 'right',
	valueField: 'id',
	queryMode: 'local',
	typeAhead: true,
	triggerAction: 'all',
	multiSelect: true,
	collapseOnSelect: true,
	fieldLabel: mw.message( 'bs-extjs-label-namespace' ).plain(),
	emptyText: mw.message( 'bs-extjs-combo-box-default-placeholder' ).plain(),
	delimiter: ', ',
	// Custom Settings
	includeAll: false,
	excludeIds: [],

	initComponent: function () {
		this.store = bs.extjs.newLocalNamespacesStore( {
			includeAll: this.includeAll,
			excludeIds: this.excludeIds
		} );
		this.callParent( arguments );
	}
} );
