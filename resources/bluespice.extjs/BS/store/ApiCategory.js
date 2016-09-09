Ext.define('BS.store.ApiCategory', {
	extend: 'BS.store.BSApi',
	apiAction: 'bs-category-store',
	fields: [
		'cat_id',
		'cat_title',
		'text',
		'cat_pages',
		'cat_subcats',
		'cat_files',
		'prefixed_text'
	]
});