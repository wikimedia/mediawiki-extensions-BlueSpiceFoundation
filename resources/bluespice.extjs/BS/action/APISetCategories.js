Ext.define( 'BS.action.APISetCategories', {
	extend: 'BS.action.APICategoryOperation',
	task: 'wikipage-setcategories',

	getDescription: function () {
		return mw.message( 'bs-deferred-action-apisetcategories-description', this.pageTitle ).parse();
	}
} );
