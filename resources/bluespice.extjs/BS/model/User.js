Ext.define('BS.model.User', {
	extend: 'Ext.data.Model',

	fields: [
		{ name: 'user_id', type: 'int' },
		{ name: 'user_name', type: 'string' },
		{ name: 'user_real_name', type: 'string' },
		{ name: 'user_registration', type: 'date', defaultValue: '19700101000000', dateFormat: 'YmdHis' },
		{ name: 'user_editcount', type: 'int' },
		{ name: 'groups', type: 'auto', defaultValue: [] },

		//legacy fields
		{ name: 'display_name', type: 'string' },
		{ name: 'page_prefixed_text', type: 'string' }
	]
});
