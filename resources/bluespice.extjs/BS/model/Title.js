Ext4.define('BS.model.Title', {
	extend: 'Ext4.data.Model',

	fields: [
		{ name: 'articleId', type: 'int' },
		{ name: 'namespaceId', type: 'int' },
		{ name: 'text', type: 'string' },
		{ name: 'prefixedText', type: 'string' }
	],

	getPrefixedText: function() {
		return this.get( 'titlePrefixedText' );
	},
	
	getLocalUrl: function() {
		return mw.util.wikiGetlink( this.getPrefixedText() );
	}
});
