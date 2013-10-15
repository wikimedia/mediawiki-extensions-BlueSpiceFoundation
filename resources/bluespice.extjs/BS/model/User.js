Ext.define('BS.model.User', {
	extend: 'Ext.data.Model',

	fields: [
		{ name: 'user_id', type: 'int' },
		{ name: 'user_name', type: 'string' },
		{ name: 'display_name', type: 'string' },
		{ name: 'page_prefixed_text', type: 'string' },
	]
});
