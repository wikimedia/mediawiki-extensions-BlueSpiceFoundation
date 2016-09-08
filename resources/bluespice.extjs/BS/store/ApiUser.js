Ext.define('BS.store.ApiUser', {
	extend: 'BS.store.BSApi',
	apiAction: 'bs-user-store',
	fields: [
		'user_id',
		'user_name',
		'user_real_name',
		'user_registration',
		'user_editcount',
		'groups', 'enabled',
		'page_link',
		'display_name',
		'page_prefixed_text'
	]
});