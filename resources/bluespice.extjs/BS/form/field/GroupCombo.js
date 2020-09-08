Ext.define( 'BS.form.field.GroupCombo', {
	extend: 'Ext.form.field.ComboBox',
	requires: [ 'BS.store.ApiGroup' ],
	triggerAction: 'all',
	displayField: 'displayname',
	valueField: 'group_name',
	allowBlank: false,
	forceSelection: true,
	queryMode: 'local',
	fieldLabel: mw.message( 'bs-extjs-label-group' ).plain(),
	emptyText: mw.message( 'bs-extjs-combo-box-default-placeholder' ).plain(),

	initComponent: function () {
		this.store = this.makeStore();
		this.callParent( arguments );
	},

	makeStore: function () {
		return new BS.store.ApiGroup();
	}
} );
