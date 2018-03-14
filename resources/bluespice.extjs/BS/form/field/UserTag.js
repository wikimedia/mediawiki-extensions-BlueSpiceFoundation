Ext.define( 'BS.form.field.UserTag', {
	extend:'Ext.form.field.Tag',
	requires: [ 'BS.store.ApiUser' ],
	displayField: 'display_name',
	valueField: 'user_name',
	initComponent: function() {
		this.store = new BS.store.ApiUser();
		this.callParent( arguments );
	}
});