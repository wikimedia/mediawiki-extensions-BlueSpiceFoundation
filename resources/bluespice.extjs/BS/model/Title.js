/**
 * This model is used to have a unified representation of all MediaWiki pages
 * one can link to. This includes SpecialPages.
 */

Ext.define('BS.model.Title', {
	extend: 'Ext.data.Model',

	/**
	 * We use the prefixedText because in MediaWiki only the combination of
	 * namespace prefix and page title is really unique. The "page_id" can not
	 * be used because titles that are not in the database yet (redlinks)
	 * default their numeric id to '0', which may be ambiguous. There are also
	 * "SpecialPage" titles wich may have their id set to '-1'
	 */
	idProperty: 'prefixedText',

	fields: [
		//Those are values we can gather from the MediaWiki 'page' table.
		//Not every Title is managed in this table!
		{ name: 'page_id', type: 'int', defaultValue: 0 },
		{ name: 'page_namespace', type: 'int', defaultValue: -99 },
		{ name: 'page_title', type: 'string', defaultValue: '' },

		//Here come custom fields that are necessary for the usage within ExtJS
		{ name: 'prefixedText', type: 'string' }, //TODO:  Maybe calculate from page_namespace and page_title?
		                                          //http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.data.Field
		{ name: 'displayText', type: 'string' },
		{ name: 'type', type: 'string' }, //'wikipage', 'specialpage'(, 'interwiki'?)
		{ name: 'page_link', type: 'string' } //An anchor HTML fragment with all kinds of meta-data
	],

	getPrefixedText: function() {
		return this.get( 'prefixedText' );
	},

	getLocalUrl: function() {
		return mw.util.getUrl( this.getPrefixedText() );
	},

	exists: function() {
		return this.get('page_id') > 0;
	},

	getNamespace: function() {
		return this.get( 'page_namespace' );
	},

	getNsText: function() {
		return bs.util.getNamespaceText( this.getNamespace() );
	}
});
