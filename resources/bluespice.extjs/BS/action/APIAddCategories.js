Ext.define( 'BS.action.APIAddCategories', {
	extend: 'BS.action.APICategoryOperation',
	task: 'wikipage-addcategories',

	getDescription: function () {
		return mw.message( 'bs-deferred-action-apiaddcategories-description', this.pageTitle ).parse();
	}
} );
