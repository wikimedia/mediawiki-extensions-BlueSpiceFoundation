Ext.define( 'BS.form.field.GroupTag', {
	extend: 'Ext.form.field.Tag',
	requires: [ 'BS.store.ApiGroup' ],
	displayField: 'displayname',
	valueField: 'group_name',

	initComponent: function () {
		this.store = this.makeStore();
		this.callParent( arguments );
	},

	makeStore: function () {
		return new BS.store.ApiGroup();
	}
} );
