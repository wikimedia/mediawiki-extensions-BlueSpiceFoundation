Ext.define( 'BS.action.APIRemoveCategories', {
	extend: 'BS.action.APICategoryOperation',
	task: 'wikipage-removecategories',

	getDescription: function () {
		return mw.message( 'bs-deferred-action-apiremovecategories-description', this.pageTitle ).parse();
	}
} );
