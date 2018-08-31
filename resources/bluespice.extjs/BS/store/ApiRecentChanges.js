Ext.define('BS.store.ApiRecentChanges', {
	extend: 'BS.store.BSApi',
	apiAction: 'bs-recentchanges-store',
	fields: [
		'user_name',
		'user_display_name',
		'user_link',
		'page_prefixedtext',
		'page_namespace',
		'page_link',
		'raw_timestamp',
		'timestamp',
		'comment_text',
		'source',
		'diff_url',
		'diff_link',
		'hist_url',
		'hist_link',
		'cur_id',
		'last_oldid',
		'this_oldid'
	]
});
