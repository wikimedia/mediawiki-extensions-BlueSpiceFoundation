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
		{ name: 'page_title', type: 'string', defaultValue: '' },
		{ name: 'page_prefixed_text', type: 'string', defaultValue: '' },
		{ name: 'page_latest', type: 'date', defaultValue: '19700101000000', dateFormat: 'YmdHis' },
		{ name: 'page_namespace', type: 'int', defaultValue: -99 },
		{ name: 'page_is_redirect', type: 'bool', defaultValue: false },
		{ name: 'page_is_new', type: 'bool', defaultValue: true },
		{ name: 'page_touched', type: 'date', defaultValue: '19700101000000', dateFormat: 'YmdHis' },
		{ name: 'page_link', type: 'string', defaultValue: '', convert: function( value, record ) {
			//This is not being calculated on the serverside for performance reasons
			var title = new mw.Title( record.get( 'page_prefixed_text' ) );
			return mw.html.element(
				'a',
				{
					'href': title.getUrl(),
					'target': '_blank',
					'data-bs-title': title.getPrefixedText(),
					'data-bs-filename': record.get( 'file_name' ),
					'data-bs-fileurl': record.get( 'file_url' )
				},
				record.get( 'file_display_text' )
			);
		}},
		//Here come custom fields that are calculated on the server side
		{ name: 'page_categories', type: 'auto', defaultValue: [] },
		{ name: 'page_categories_links', type: 'auto', defaultValue: [], convert: function( value, record ) {
			//This is not being calculated on the serverside for performance reasons
			var categories = record.get( 'page_categories' );
			var categoryLinks = [];
			for( var i = 0; i < categories.length; i++ ) {
				var category = categories[i];
				var title = new mw.Title( category, bs.ns.NS_CATEGORY );
				var icon = mw.html.element( 'span', { 'class' : 'bs-icon-tag' }, '' );
				var link = mw.html.element(
					'a',
					{
						'href': title.getUrl(),
						'target': '_blank',
						'data-bs-title': title.getPrefixedText()
					},
					title.getNameText()
				);

				categoryLinks.push(
					mw.html.element( 'span', {}, new mw.html.Raw( icon + link ) )
				);
			}

			return categoryLinks;
		}},

		//Those are values we can gather from the MediaWiki 'image' table.
		{ name: 'file_url', type: 'string', defaultValue: '' },
		{ name: 'file_name', type: 'string' },
		{ name: 'file_size', type: 'int', defaultValue: 0 },
		{ name: 'file_bits', type: 'int', defaultValue: 0 },
		{ name: 'file_user', type: 'int', defaultValue: 0 },
		{ name: 'file_width', type: 'int', defaultValue: 0 },
		{ name: 'file_height', type: 'int', defaultValue: 0 },
		{ name: 'file_mimetype', type: 'string', defaultValue: 'unknown/unknown' },
		{ name: 'file_user_text', type: 'string' },
		{ name: 'file_extension', type: 'string', defaultValue: '' },
		{ name: 'file_timestamp', type: 'date', defaultValue: '19700101000000', dateFormat: 'YmdHis' },
		{ name: 'file_mediatype', type: 'string', defaultValue: '' },
		{ name: 'file_description', type: 'string', defaultValue: '' },
		{ name: 'file_thumbnail_url', type: 'string', defaultValue: '' },

		//Here come custom fields that are calculated on the server side
		{ name: 'file_display_text', type: 'string', defaultValue: '' }, //TODO: Maybe fallback to 'file_name'
		{ name: 'file_user_display_text', type: 'string', defaultValue: '' },
		{ name: 'file_user_link', type: 'string', defaultValue: '', convert: function( value, record ) {
			//This is not being calculated on the serverside for performance reasons
			var title = new mw.Title( record.get( 'file_user_text' ), bs.ns.NS_USER );
			var icon = mw.html.element( 'span', { 'class' : 'bs-icon-user' }, '' );
			var link = mw.html.element(
				'a',
				{
					'href': title.getUrl(),
					'target': '_blank',
					'data-bs-title': title.getPrefixedText(),
					'data-bs-username': record.get( 'file_user_text' )
				},
				record.get( 'file_user_display_text' )
			);
			return mw.html.element( 'span', {}, new mw.html.Raw( icon + link ), '' );
		}}
	]

	//TODO: Implement getter
});