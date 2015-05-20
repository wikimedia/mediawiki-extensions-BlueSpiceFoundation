/**
 * This model is used to have a unified representation of all MediaWiki repo
 * files. The information is a combination of the page and the file table
 */

Ext.define('BS.model.File', {
	extend: 'Ext.data.Model',

	idProperty: 'file_name',

	fields: [
		//Those are values we can gather from the MediaWiki 'page' table.
		{ name: 'page_id', type: 'int', defaultValue: 0 },
		{ name: 'page_namespace', type: 'int', defaultValue: -99 },
		{ name: 'page_title', type: 'string', defaultValue: '' },
		{ name: 'page_is_new', type: 'bool', defaultValue: true },
		{ name: 'page_touched', type: 'date', defaultValue: '19700101000000', dateFormat: 'YmdHis' },
		{ name: 'page_is_redirect', type: 'bool', defaultValue: false },
		{ name: 'page_latest', type: 'date', defaultValue: '19700101000000', dateFormat: 'YmdHis' },

		//Here come custom fields that are calculated on the server side
		{ name: 'page_categories', type: 'array', defaultValue: [] },

		{ name: 'file_name', type: 'string' },
		{ name: 'file_size', type: 'int', defaultValue: 0 },
		{ name: 'file_bits', type: 'int', defaultValue: 0 },
		{ name: 'file_user', type: 'int', defaultValue: 0 },
		{ name: 'file_width', type: 'int', defaultValue: 0 },
		{ name: 'file_height', type: 'int', defaultValue: 0 },
		{ name: 'file_mimetype', type: 'string', defaultValue: 'unknown/unknown' },
		{ name: 'file_metadata', type: 'object', defaultValue: {} },
		{ name: 'file_extension', type: 'string', defaultValue: '' },
		{ name: 'file_timestamp', type: 'date', defaultValue: '19700101000000', dateFormat: 'YmdHis' },
		{ name: 'file_mediatype', type: 'string', defaultValue: '' },
		{ name: 'file_description', type: 'string', defaultValue: '' },
		{ name: 'file_display_text', type: 'string', defaultValue: '' }, //TODO: Maybe fallback to 'file_name'
		{ name: 'file_thumbnail_url', type: 'string', defaultValue: '' }
	]

	//TODO: Implement getter
});